<?php

namespace App\Services\Api;

use App\Models\Order;
use App\Models\User;
use App\Models\UserTokenPayment;
use App\Services\BaseService;
use App\Exceptions\GeneralException;
use App\Models\ContactUs;
use App\Models\Rating;
use App\Models\SmsOtp;
use App\Models\SmsFirebaseInfo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Exception;
use App\Events\ContactUs\ContactUsEmail;
use App\Jobs\OffWorkerWithoutJob;

/**
 * Class UserService.
 */
class UserService extends BaseService
{
    /**
     * UserService constructor.
     *
     * @param  User  $user
     */
    public function __construct(User $user)
    {
        $this->model = $user;
    }

    /**
     * @param  array  $data
     *
     * @return User
     */
    public function createWorker(array $data = []): User
    {
        DB::beginTransaction();

        try {
            $user = $this->model->create([
                'name' => $data['first_name'] . ' ' . $data['last_name'],
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'password' => Hash::make($data['password']),
                'phone' => $data['phone'],
                'gender' => $data['gender'],
                'address' => $data['address'],
                'number_id' => $data['number_id'],
                'type_number_id' => $data['type_number_id'],
                'id_card_before' => $data['img_id_before'] ?? null,
                'id_card_after' => $data['img_id_after'] ?? null,
                'user_type' => User::IS_WORKER,
                'status' => User::IS_PENDING,
                'device_token' => $data['device_token'] ?? null,
                'nation_code' => !empty($data['nation_code']) ? strtolower($data['nation_code']) : 'vn'
            ]);

            // Save user profile
            if ($user) {
                $user->profile()->create([
                    'user_id' => $user->id,
                    'birth_day' => $data['bod'] ?? null,
                    'avatar' => $data['img_avatar'] ?? null
                ]);
            }
        } catch (Exception $e) {
            throw new Exception('Error ' . $e->getMessage());
            DB::rollBack();
            throw new GeneralException(__('There was a problem create this worker. Please try again.'));
        }

        DB::commit();

        return $user;
    }

    /**
     * @param  array  $data
     *
     * @return User
     */
    public function createUser(array $data = []): User
    {
        DB::beginTransaction();

        try {
            $user = $this->model->create([
                'name' => $data['fullname'],
                'first_name' => !empty($data['firstname']) ? $data['firstname'] : $data['fullname'],
                'last_name' => $data['lastname'] ?? '',
                'password' => Hash::make($data['password']),
                'phone' => $data['phone'],
                'user_type' => User::IS_USER,
                'status' => User::IS_ACTIVE,
                'device_token' => $data['device_token'] ?? null,
                'nation_code' => !empty($data['nation_code']) ? strtolower($data['nation_code']) : 'vn'
            ]);

            // Save user profile
            if ($user) {
                $user->profile()->create([
                    'user_id' => $user->id
                ]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            throw new GeneralException(__('There was a problem create this worker. Please try again.'));
        }

        DB::commit();

        return $user;
    }

    /**
     * @param $request
     * 
     * @return boolean|array
     */
    public function login($request)
    {
        $credentials = [
            'phone' => $request->phone,
            'password' => $request->password,
            'user_type' => $request->header('UserType', ''),
        ];

        if (!Auth::attempt($credentials)) {
            return false;
        }

        $user = $request->user();
        $user->update([
            'device_token' => $request->header('DeviceToken', '') ?? null,
            'device_platform' => $request->devicePlatform ?? null,
            'app_version' => $request->appVersion ?? null
        ]);

        $tokenResult = $user->createToken('access_token');
        $token = $tokenResult->token;
        if ($request->remember_me) {
            $token->expires_at = Carbon::now()->addDays(1);
        }

        $token->save();

        // Set access_token_user to header
        $request->headers->set('access_token_user', $tokenResult->accessToken);
        // Update worker status on
        $this->updateWorkerStatus($user, ['status' => User::WORKER_ON, 'device_token' => $request->header('DeviceToken', '')]);

        return [
            'access_token' => $tokenResult->accessToken,
            'expires_at' => Carbon::now()->toDateTimeString(),
            'user_data' => getUserDataObject($user->id),
            'data_post' => array(
                'devicePlatform' => $request->devicePlatform,
                'appVersion' => $request->appVersion
            )
        ];
    }

    /**
     * Refresh token
     */
    public function refreshToken($request)
    {
        // Set access_token_user to header
        $user = $request->user();
        $tokenResult = $user->createToken('access_token');
        $token = $tokenResult->token;
        $token->save();

        // Set access_token_user to header
        $request->headers->set('access_token_user', $tokenResult->accessToken);

        return [
            'access_token' => $tokenResult->accessToken,
        ];
    }

    /**
     * @param $user
     * @param array $data
     * @return $user
     */
    public function updateUserLocation(User $user, array $data = []): User
    {
        DB::beginTransaction();

        try {
            $user->update([
                'longtitude' => $data['longtitude'],
                'latitude' => $data['latitude']
            ]);

            // Create User Location
            // $user->location()->updateOrCreate([
            //     'user_id' => $user->id,
            //     'longtitude' => $data['longtitude'],
            //     'latitude' => $data['latitude']
            // ]);
        } catch (Exception $e) {
            DB::rollBack();
            throw new GeneralException(__('There was a problem updating this user. Please try again.'));
        }

        DB::commit();

        return $user;
    }

    /**
     * Update status worker
     * @param User
     * @param array $data
     * @return User
     */
    public function updateWorkerStatus(User $user, array $data = [])
    {
        if ($user->user_type == User::IS_WORKER) {
            $user->update([
                'worker_status' => $data['status'],
                'device_token' => $data['device_token']
            ]);
            // Add queue set offline worker without job
            OffWorkerWithoutJob::dispatch()->delay(now()->addMinutes(config('constant.time_run_queue')));

            return true;
        }

        return false;
    }

    /**
     * Get list worker for user
     * @param $latitude
     * @param $longtitude
     * @param $radius
     * @return collection $workers
     */
    public function getListWorker($latitude, $longtitude, $nationeCode = 'vn', $radius = 50)
    {
        return $this->model::query()
            ->selectRaw("id,  nation_code, final_rating, first_name, last_name, phone, address, user_type, latitude, longtitude, device_token,
                ( 6371 * acos( cos( radians(?) ) *
                cos( radians( latitude ) )
                * cos( radians( longtitude ) - radians(?)
                ) + sin( radians(?) ) *
                sin( radians( latitude ) ) )
                ) AS distance", [$latitude, $longtitude, $latitude])
            ->isWorker()
            ->isActive()
            ->workerOn()
            ->workingOff()
            ->where('nation_code', $nationeCode)
            ->having("distance", "<=", $radius)
            ->orderBy("distance", 'asc')
            ->orderBy("final_rating", 'desc')
            ->get();
    }

    /**
     * Get worker nearest
     * @param $latitude
     * @param $longtitude
     * @param $radius
     * @return User
     */
    public function getWorkerNearest($latitude, $longtitude, $workerIds, $nationeCode = 'vn', $radius = 50)
    {
        return $this->model::query()
            ->selectRaw("id, nation_code, final_rating, first_name, last_name, phone, address, user_type, latitude, longtitude, device_token, device_platform,
                ( 6371 * acos( cos( radians(?) ) *
                cos( radians( latitude ) )
                * cos( radians( longtitude ) - radians(?)
                ) + sin( radians(?) ) *
                sin( radians( latitude ) ) )
                ) AS distance", [$latitude, $longtitude, $latitude])
            ->isWorker()
            ->workingOff()
            ->workerOn()
            ->isActive()
            ->where('nation_code', $nationeCode)
            ->whereNotIn('id', $workerIds)
            ->having("distance", "<=", $radius)
            ->orderBy("distance", 'asc')
            ->orderBy("final_rating", 'desc')
            ->first();
    }

    /**
     * send OTP sms
     * @param array $data
     * @return JsonResponse
     */
    public function sendOtp(array $data = [])
    {
        $type = $data['type'];
        $phone = str_replace('+84', '0', $data['phone']);
        if (substr($phone, 0, 2) == '00') {
            $phone = str_replace(substr($phone, 0, 2), '0', $phone);
        }
        $user = $this->model->where('phone', $data['phone'])->where('user_type', $data['user_type'])->first();
        $otpRandom = random_int(1000, 9999);

        if (($type == 'USER_SIGNUP' && is_null($user)) || ($type == 'FORGOT_PASSWORD' && !is_null($user))) {

            //add record table sms_otps
            SmsOtp::create([
                'phone' => $data['phone'], //origin phone mobile sent
                'otp' => $otpRandom,
                'expired_date' => Carbon::now()->addMinutes(5),
                'type' => $type
            ]);

            // Call api otp voice
            $apiKey = config('constant.sms.api_key');
            $campaignID = config('constant.sms.campaign_id');
            $urlOtpVoice = config('constant.sms.url_otp_voice');
            $extraHeaders = [
                'api-key' => $apiKey
            ];

            $options = [
                'json' => [
                    'campaign_id' =>  $campaignID,
                    'otp' => $otpRandom,
                    'customer_info' => [
                        'sdt' => $phone
                    ],
                ]
            ];

            $response = callGuzzleHttp('POST', $urlOtpVoice, $options, $extraHeaders);

            if ($response['success']) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verify OTP
     * 
     * @param array $data
     * 
     * @return boolean
     */
    public function verifyOtp(array $data = [])
    {
        if (empty($data['phone']) || empty($data['type'])) {
            return false;
        }

        $smsInfo = SmsFirebaseInfo::where([
            ['phone', $data['phone']],
            ['type', 'USER_GET_OTP'],
            ['status', 0],
            ['expired_date', '>=', getCurrentTime()],
        ])->latest('id', 'desc')->first();

        if (empty($smsInfo) || is_null($smsInfo)) {
            return false;
        }

        // Handle verify otp
        $sessionInfo = $smsInfo->session_info;
        $option = [
            'body' => json_encode([
                'code' => $data['otp'],
                'sessionInfo' => $sessionInfo
            ])
        ];

        $response = callGuzzleHttp('POST', config('constant.firebase.url_verify_code'), $option);
        if (isset($response) && isset($response['idToken'])) {
            $smsInfo->update(array(
                'code' => $data['otp'],
                'type' => $data['type'],
                'status' => 1
            ));
            return true;
        }

        return false;
    }

    /**
     * Forgot pass
     * 
     * @param array $data
     * 
     * @return boolean
     */
    public function forgotPass(array $data = [])
    {
        $otp = SmsFirebaseInfo::where([
            ['code', $data['otp']],
            ['type', 'FORGOT_PASSWORD'],
            ['status', 1]
            // ['expired_date', '>=', getCurrentTime()]
        ])->latest('id', 'desc')->first();

        $user = $this->model->where('phone', $data['phone'])
            ->where('user_type', $data['user_type'])
            ->first();

        if (!is_null($otp) && !is_null($user)) {
            $user->update([
                'password' => Hash::make($data['new_password'])
            ]);

            //update smsOTP Failed
            // $otp->update([
            //     'status' => 0
            // ]);

            return true;
        }

        return false;
    }

    /**
     * Get list location of user history
     * @param User $user
     * @return List location
     */
    public function getListLocation(User $user)
    {
        return Order::where('user_id', $user->id)
            ->with('detail', function ($q) {
                $q->select('id', 'order_id', 'longtitude', 'latitude');
            })
            ->select('id', 'address', 'address_title')
            ->latest('id', 'desc')
            ->take(20)
            ->get();
    }

    /**
     * Check user signuped
     * @param array $data
     * @return boolean
     */
    public function checkUserSignuped(array $data)
    {
        $user = $this->model->where('phone', $data['phone'])->where('user_type', $data['type'])->first();
        return isset($user) ? 1 : 0;
    }

    /**
     * Change password
     * @param User $user
     * @param $passwordOld
     * @param $passwordNew
     */
    public function changePass(User $user, $passwordOld, $passwordNew)
    {
        if (Hash::check($passwordOld, $user->password)) {
            return $user->update([
                'password' => Hash::make($passwordNew)
            ]);
        }

        return false;
    }

    /**
     * Get user by id
     * @param $userID
     * @return User
     */
    public function getUserByID($userID): User
    {
        return $this->model->findOrFail($userID);
    }

    /**
     * Update profile
     * @param User $user
     * @param array $data
     * @return User
     */
    public function updateProfile(User $user, array $data = [])
    {
        DB::beginTransaction();

        try {
            if ($user->user_type == User::IS_USER) {
                $user->update([
                    'name' => $data['fullname'] ?? $user->name,
                    'first_name' => $data['first_name'] ?? $user->first_name,
                    'last_name' => $data['last_name'] ?? $user->last_name,
                    'gender' => $data['gender'] ?? $user->gender,
                    'address' => $data['address'] ?? $user->address
                ]);

                if (isset($data['img_avatar'])) {
                    $user->profile()->update([
                        'avatar' => $data['img_avatar']
                    ]);
                }
            }
        } catch (Exception $e) {
            DB::rollBack();
            throw new GeneralException(__('There was a problem update profile this user. Please try again.'));
        }

        DB::commit();

        return getUserDataObject($user->id);
    }

    /**
     * Check user logged
     */
    public function checkLogged(User $user)
    {
        return !is_null($user) ? true : 'Unauthorized';
    }

    /**
     * Create rating and calculate final rating for worker
     * 
     * @param array $data
     * 
     * @return boolean
     */
    public function createRating(array $data = [])
    {
        Rating::create([
            'order_id' => $data['order_id'],
            'user_id' => $data['user_id'],
            'rating' => $data['rating'],
            'note' => $data['note']
        ]);

        $worker = $this->model::query()->withAvg('ratings', 'rating')
            ->where('id', $data['user_id'])
            ->first();
        
        if($worker && (!empty($worker->ratings_avg_rating) || !is_null($worker->ratings_avg_rating))){
            $worker->update([
                'final_rating' => round($worker->ratings_avg_rating, 1)
            ]);
        }

        return true;
    }

    /**
     * Get list card
     *
     * @param int $userId
     *
     * @return object
     */
    public function getListCard(int $userId)
    {
        //the phai su dung gan nhat trong 5,5 thang
        return UserTokenPayment::where('user_id', $userId)
            ->whereRaw('DATEDIFF(CURDATE(), last_used_date) <= 165')
            ->select('id', 'pay_token', 'bank_type', 'card_no', 'card_brand', 'last_used_date', 'created_at')
            ->get();
    }

    /**
     * Delete token payment card
     *
     * @param int $id
     *
     * @return bool
     */
    public function deleteTokenCardByID(int $id)
    {
        $tokenCardData = UserTokenPayment::where('id', $id)->first();

        if (!is_null($tokenCardData)) {
            // $decryptPayToken = decrypt3DES($tokenCardData->pay_token);
            // $encryptedPayToken = encrypt3DES($decryptPayToken);

            // $options = [
            //     'json' => [
            //         'mer_id' => config('constant.vnpt.mer_id'),
            //         'payType' => $tokenCardData->bank_type,
            //         'payToken' => $encryptedPayToken,
            //         'merchantToken' => hash('sha256', config('constant.vnpt.mer_id') . $tokenCardData->bank_type . $encryptedPayToken . config('constant.vnpt.endcode_key'))
            //     ]
            // ];

            try {
                // $response = callGuzzleHttp('POST', 'https://sandbox.megapay.vn/pg_was/deleteTokenAPI.do', $options);
                // if(isset($response['resultCd']) && $response['resultCd'] == '00_000'){
                //     $tokenCardData->delete();

                //     return true;
                // }
                $tokenCardData->delete();

                return true;
            } catch (Exception $e) {
                return false;
            }
        }

        return false;
    }

    /**
     * Send mail contact us to admin
     *
     * @param array $data
     * @param mixed $user
     *
     * @return mixed
     */
    public function sendMailContactUs(array $data = [], $user)
    {
        ContactUs::create([
            'user_id' => $user->id,
            'title' => $data['title'],
            'content' => $data['content'],
        ]);

        return event(new ContactUsEmail($data));
    }

    /**
     * Set offline status list worker online more than hours without job
     * 
     * @return boolean
     */
    public function setOfflineStatusWorkerWithoutJob()
    {
        return $this->model::query()
            ->isWorker()
            ->workerOn()
            ->where('updated_at', '<=', now()->subMinutes(config('constant.worker_online_more_minutes')))
            ->update([
                'worker_status' => 0
            ]);
    }
}
