<?php

use App\Models\Order;
use App\Models\Page;
use App\Models\Role;
use App\Models\User;
use App\Models\UserTokenPayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Laravel\Passport\Token;

if (!function_exists('includeFilesInFolder')) {
    /**
     * Loops through a folder and requires all PHP files
     * Searches sub-directories as well.
     *
     * @param $folder
     */
    function includeFilesInFolder($folder)
    {
        try {
            $rdi = new RecursiveDirectoryIterator($folder);
            $it = new RecursiveIteratorIterator($rdi);

            while ($it->valid()) {
                if (!$it->isDot() && $it->isFile() && $it->isReadable() && $it->current()->getExtension() === 'php') {
                    require $it->key();
                }

                $it->next();
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}

if (!function_exists('includeRouteFiles')) {

    /**
     * @param $folder
     */
    function includeRouteFiles($folder)
    {
        includeFilesInFolder($folder);
    }
}

if (!function_exists('getPathImage')) {
    function getPathImage($value)
    {
        return asset('/uploads/images/' . $value);
    }
}

if (!function_exists('getUserDataObject')) {
    function getUserDataObject($userId)
    {
        $user = User::find($userId);
        if ($user) {
            return (object) [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'phone' => $user->phone,
                'address' => $user->address,
                'user_type' => $user->user_type,
                'latitude' => $user->latitude,
                'longtitude' => $user->longtitude,
                'gender' => $user->gender,
                'img_avatar' => (!is_null($user->profile) && !empty($user->profile->avatar)) ? asset('/uploads/images/' . $user->profile->avatar) : null,
                'status' => $user->status,
                'worker_working_status' => array(
                    "is_working" => $user->is_working,
                    "worker_status" => $user->worker_status
                )
            ];
        }

        return null;
    }
}

if (!function_exists('getUserDetailDataObject')) {
    function getUserDetailDataObject($userId)
    {
        $user = User::with(['profile' => function ($query) {
            $query->select('id', 'user_id', 'avatar', 'birth_day');
        }])
            ->where('id', $userId)
            ->first();

        return (object) [
            'id' => $user->id,
            'fullname' => $user->name,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'phone' => $user->phone,
            'address' => $user->address,
            'user_type' => $user->user_type,
            'latitude' => $user->latitude,
            'longtitude' => $user->longtitude,
            'img_card_before' => !is_null($user->id_card_before) ? asset('/uploads/images/' . $user->id_card_before) : null,
            'img_card_after' => !is_null($user->img_card_after) ? asset('/uploads/images/' . $user->img_card_after) : null,
            'img_avatar' => (!is_null($user->profile) && !empty($user->profile->avatar)) ? asset('/uploads/images/' . $user->profile->avatar) : null,
            'birth_day' => !is_null($user->profile) ? $user->profile->birth_day : null,
            'status' => $user->status,
            'worker_working_status' => array(
                "is_working" => $user->is_working,
                "worker_status" => $user->worker_status
            )
        ];
    }
}

if (!function_exists('getOrderDataObject')) {
    function getOrderDataObject($orderId)
    {
        $order = Order::where('id', $orderId)
            ->with('detail')
            ->first();

        return (object) [
            'id' => $order->id,
            'price' => $order->detail->price,
            'status' => config('constant.order_status_id.' . $order->detail->status_id),
        ];
    }
}

if (!function_exists('getOrderDetailFullData')) {
    function getOrderDetailFullData($orderId, $urlPayment = '')
    {
        $order = Order::where('id', $orderId)
            ->with('detail')
            ->first();

        return (object) [
            'id' => $order->id,
            'price' => $order->detail->price,
            'currency' => $order->detail->currency ?? 'VND',
            'amount_detail' => array(
                'amount' => $order->detail->amount,
                'amount_tip' => $order->tip_status == 1 ? $order->detail->amount_tip : 0,
                'tip' => $order->tip_status == 1 ? $order->detail->tip : null,
                'tip_type' => $order->tip_status == 1 ? $order->detail->tip_type : null,
                'fee_app' => $order->detail->fee_app
            ),
            'rating_detail' => array(
                'user_rating_worker' => 5,
                'worker_rating_user' => 5
            ),
            'status' => config('constant.order_status_id.' . $order->order_status),
            'order_status' => $order->order_status,
            'payment_status' => $order->payment_status,
            'address' => $order->address,
            'latitude' => $order->detail->latitude,
            'longtitude' => $order->detail->longtitude,
            'note_description' => $order->detail->note_description,
            'service_name' => $order->detail->service_name,
            'service_child_name' => $order->detail->service_child_name,
            'created_at' => $order->created_at,
            'begin_at' => $order->detail->begin_at,
            'working_time' => $order->detail->working_time,
            'working_time_detail' => array(
                'total_hour' => $order->detail->working_total_hour,
                'total_minute' => $order->detail->working_total_minute
            ),
            'url_payment_with_otp' => $urlPayment
        ];
    }
}


if (!function_exists('getCurrentTime')) {
    function getCurrentTime()
    {
        return Carbon::now()->toDateTimeString();
    }
}

if (!function_exists('getStartDate')) {
    function getStartDate()
    {
        return Carbon::now()->subMonth()->format('m-d-Y');
    }
}

if (!function_exists('getEndDate')) {
    function getEndDate()
    {
        return Carbon::now()->format('m-d-Y');
    }
}

if (!function_exists('getUriParamsQuery')) {
    function getUriParamsQuery()
    {
        $url = parse_url(URL::full());
        return $url['query'] ?? '';
    }
}

if (!function_exists('checkDevice')) {
    /**
     * Check device user use
     *
     * @return string
     */
    function checkDevice()
    {
        $agent = new Agent();
        return $agent->isMobile() ? 'mobile' : 'desktop';
    }
}

if (!function_exists('checkIsMobile')) {
    /**
     * Check is mobile
     *
     * @return bool
     */
    function checkIsMobile()
    {
        $agent = new Agent();
        return $agent->isMobile() ? true : false;
    }
}

if (!function_exists('getCurrentRouteName')) {
    /**
     * Get current route name
     *
     * @return string
     */
    function getCurrentRouteName()
    {
        return Route::currentRouteName();
    }
}

if (!function_exists('getRouteNameUserDetail')) {
    /**
     * Get detail route name for user with type
     *
     * @param string $userType
     *
     * @return string
     */
    function getRouteNameUserDetail($userType)
    {
        return $userType == User::IS_WORKER ? 'admin.users.worker-detail' : 'admin.users.customer-detail';
    }
}

if (!function_exists('formatDateTime')) {
    /**
     * Format date time
     *
     * @param $time
     * @param string $format
     *
     * @return string|null
     */
    function formatDateTime($time = '', string $format = 'm-d-Y H:i:s')
    {
        return !empty($time) ? Carbon::parse($time)->format($format) : null;
    }
}

if (!function_exists('strPadLeftZero')) {
    /**
     * Format number pad left
     *
     * @return string
     */
    function strPadLeftZero(int $num, int $maxNum)
    {
        return str_pad($num, $maxNum, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('getPagesMenuLeft')) {
    /**
     * Get list menu left
     *
     * @return object
     */
    function getPagesMenuLeft()
    {
        // For admin user
        if (request()->user()->hasRole(['root', 'admin'])) {
            return Cache::remember(config('constant.menu.cache.admin'), Carbon::now()->addDay(), function () {
                return Page::select('id', 'name', 'slug', 'route_name', 'order')
                    ->with('children')
                    ->where('parent_id', false)
                    ->orderBy('order', 'asc')
                    ->get();
            });
        }

        // For account user
        $cacheKey = config('constant.menu.cache.account');
        $user = request()->user();
        if ($user->userRole) {
            $cacheKey = 'menu_account_' . $user->userRole->name;
        }

        return Cache::remember($cacheKey, Carbon::now()->addDay(), function () use ($user) {
            $role = Role::with('page')->where('name', $user->getRoleNames()->first())->first();
            $page_ids = [];
            if ($role && !is_null($role->page)) {
                $page_ids = json_decode($role->page->page_ids);
            }

            return Page::select('id', 'name', 'slug', 'route_name', 'order')
                ->with(['children' => function ($query) use ($page_ids) {
                    $query->whereIn('id', $page_ids);
                }])
                ->whereIn('id', $page_ids)
                ->where('parent_id', false)
                ->whereNotNull('slug')
                ->orderBy('order', 'asc')
                ->get();
        });
    }
}

if (!function_exists('isSuperAdmin')) {
    /**
     * Check user is Super Admin
     *
     * @return bool
     */
    function isSuperAdmin($user)
    {
        return $user->user_type == 'super_admin' ? true : false;
    }
}

if (!function_exists('isAdmin')) {
    /**
     * Check user is Admin
     *
     * @return bool
     */
    function isAdmin($user)
    {
        return $user->user_type == 'admin' ? true : false;
    }
}

if (!function_exists('isAccountOrEditor')) {
    /**
     * Check user is account or editor
     *
     * @return bool
     */
    function isAccountOrEditor($user)
    {
        return ($user->user_type == 'account' || $user->user_type == 'editor') ? true : false;
    }
}

if (!function_exists('isBase64')) {
    /**
     * Check string is base64
     *
     * @return string
     */
    function isBase64($str)
    {
        return base64_encode(base64_decode($str, true)) === $str ? true : false;
    }
}

if (!function_exists('isImage')) {
    /**
     * Check is image
     *
     * @return bool
     */
    function isImage($fileName)
    {
        return (!empty($fileName) && preg_match('/(\.jpg|\.png|\.bmp)$/', $fileName)) ? true : false;
    }
}

if (!function_exists('getPathImageUpload')) {
    /**
     * Get path image upload
     *
     * @return string
     */
    function getPathImageUpload($value)
    {
        return app()->environment('local') ? "https://helpinghnds-dev.giacongphanmem.vn/uploads/images/$value" : asset('/uploads/images') . "/$value";
    }
}

if (!function_exists('userCustomer')) {
    function userCustomer()
    {
        return auth('customer')->user();
    }
}

if (!function_exists('userWorker')) {
    function userWorker()
    {
        return auth('worker')->user();
    }
}

if (!function_exists('randomStringAndNumber')) {
    /**
     * Random string
     *
     * @return string
     */
    function randomStringAndNumber()
    {
        return Str::random(20);
    }
}

if (!function_exists('formatCurrency')) {
    /**
     * Format currency
     *
     * @param mixed $value
     * @param mixed $nationCode
     *
     * @return string
     */
    function formatCurrency($value, $nationCode = 'vn')
    {
        // switch ($nationCode) {
        //     case 'vn':
        //         $value = number_format($value, 0, ',', '.');
        //         break;
        //     case 'us':
        //         $value = '$' . number_format($value, 0, ',', '.');
        //         break;
        //     default:
        //         $value = number_format($value, 0, ',', '.') ;
        //         break;
        // }

        return number_format($value, 0, ',', '.');
    }
}

if (!function_exists('getLastCardUsed')) {
    /**
     * Get last card used
     *
     * @param string $paymentToken
     *
     * @return string
     */
    function getLastCardUsed(string $paymentToken)
    {
        return Order::where('token_payment', $paymentToken)
            ->latest()->value('created_at');
    }
}

if (!function_exists('encrypt3DES')) {
    /**
     * API Public For Encrypt Triple 3DES
     *
     * @param string $str
     *
     * @return string
     */
    function encrypt3DES(string $str)
    {
        $options = [
            'json' => [
                'dataFormat' => 'Hex',
                'mode' => 'ECB',
                'secretKey' => substr(config('constant.vnpt.endcode_key'), strlen(config('constant.vnpt.endcode_key')) - 24, 24),
                'textToEncrypt' => $str
            ]
        ];

        $response = callGuzzleHttp('POST', 'https://www.devglan.com/online-tools/des-encrypt', $options);

        return isset($response['output']) ? $response['output'] : $str;
    }
}

if (!function_exists('decrypt3DES')) {
    /**
     * API Public For Decrypt Triple 3DES
     *
     * @param string $str
     *
     * @return string
     */
    function decrypt3DES(string $str)
    {
        $options = [
            'json' => [
                'dataFormat' => 'Hex',
                'mode' => 'ECB',
                'secretKey' => substr(config('constant.vnpt.endcode_key'), 0, 24),
                'textToDecrypt' => $str
            ]
        ];

        $response = callGuzzleHttp('POST', 'https://www.devglan.com/online-tools/des-decrypt', $options);

        return isset($response['output']) ? base64_decode($response['output']) : $str;
    }
}

if (!function_exists('getUserByAccessToken')) {
    /**
     * Get user by accessToken
     *
     * @param $accessToken
     *
     * @return User
     */
    function getUserByAccessToken($accessToken = '')
    {
        if (empty($accessToken) || is_null($accessToken)) {
            return null;
        }

        $tokenParts = explode('.', $accessToken);
        if (!isset($tokenParts[1])) {
            return null;
        }

        $tokenHeader = $tokenParts[1];
        $tokenHeaderJson = base64_decode($tokenHeader);
        $tokenHeaderArr = json_decode($tokenHeaderJson, true);
        if (is_array($tokenHeaderArr) && isset($tokenHeaderArr['jti'])) {
            $tokenID = $tokenHeaderArr['jti'];
            return Token::find($tokenID)->user;
        }

        return null;
    }
}

if(!function_exists('getTokenFromCardExistStripe')){
    /**
     * Get token from card exist
     * 
     * @param array $data
     * 
     * @return string
     */
    function getTokenFromCardExistStripe(array $data = [])
    {
        $client = new \GuzzleHttp\Client();
            $pubKey = config('constant.stripe.key');

            $headers = [
                'Pragma' => 'no-cache',
                'Origin' => 'https://js.stripe.com',
                'Accept-Encoding' => 'gzip, deflate',
                'Accept-Language' => 'en-US,en;q=0.8',
                'User-Agent' => $data['user_agent'],
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Accept' => 'application/json',
                'Cache-Control' => 'no-cache',
                'Referer' => 'https://js.stripe.com/v2/channel.html?stripe_xdm_e=http%3A%2F%2Fwww.beanstalk.dev&stripe_xdm_c=default176056&stripe_xdm_p=1',
                'Connection' => 'keep-alive'
            ];
            $postBody = [
                'key' => $pubKey,
                'payment_user_agent' => 'stripe.js/Fbebcbe6',
                'card[number]' => $data['card_number'],
                'card[cvc]' => $data['cvc'],
                'card[exp_month]' => $data['exp_month'],
                'card[exp_year]' => $data['exp_year'],
            ];

            $response = $client->post('https://api.stripe.com/v1/tokens', [
                'headers' => $headers,
                'form_params' => $postBody
            ]);

            $response = json_decode($response->getbody()->getContents());

            return $response->id;
    }
}

if(!function_exists('getMethodNamePayment')){
    /**
     * Get method name payment from supplier
     * 
     * @param string $payToken
     * @pram int $userId
     * 
     * @return string
     */
    function getMethodNamePayment($payToken, int $userId)
    {
        return UserTokenPayment::query()
            ->where('user_id', $userId)
            ->where('pay_token', $payToken)
            ->value('payment_3rd');
    }
}

if(!function_exists('formatBytes')){
    function formatBytes($bytes, $decimals = 2) { 
        $size = ['B','KB','MB','GB','TB','PB','EB','ZB','YB'];
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' .@$size[$factor];
    } 
}

if(!function_exists('getAllFilesLogFullPath')){
    function getAllFilesLogFullPath()
    {
        $logViewer = new \Rap2hpoutre\LaravelLogViewer\LaravelLogViewer;
        return $logViewer->getFiles();
    }
}