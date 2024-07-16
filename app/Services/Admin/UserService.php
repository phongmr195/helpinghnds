<?php

namespace App\Services\Admin;

use App\Models\User;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\SmsFirebaseInfo;
use App\Models\UserTokenPayment;
use App\Services\BaseService;
use App\Exceptions\GeneralException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Exception;
use Carbon\Carbon;


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
    public function createUser(array $data = []): User
    {
        DB::beginTransaction();

        try {
            $user = $this->model->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone' => $data['phone'],
                'longtitude' => $data['longtitude'],
                'latitude' => $data['latitude'],
                'country_id' => $data['country_id'],
                'user_type' => $data['user_type'],
                'avatar' => $data['avatar'],
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            throw new GeneralException(__('There was a problem create this user. Please try again.'));
        }

        DB::commit();

        return $user;
    }

    /**
     * @param $user
     * @param array $data
     * @return $user
     */
    public function updateUser(User $user, array $data = []): User
    {
        DB::beginTransaction();

        $dataUpdate = [];

        foreach ($data as $key => $value) {
            if (!is_null($value)) {
                if ($key == 'password') {
                    $value = Hash::make($value);
                }
                $dataUpdate[$key] = $value;
            }
        }

        try {
            $user->update($dataUpdate);
            if (isset($data['avatar'])) {
                $user->profile()->update([
                    'avatar' => $data['avatar']
                ]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            throw new GeneralException(__('There was a problem updating this user. Please try again.'));
        }

        DB::commit();

        return $user;
    }

    /**
     * List users
     */
    public function listUser()
    {
        return $this->model->select('id', 'name', 'first_name', 'last_name', 'address', 'email', 'phone', 'gender', 'latitude', 'longtitude', 'number_id', 'type_number_id', 'id_card_before', 'id_card_after', 'user_type')
            ->with('profile')
            ->latest('id')
            ->get();
    }

    /**
     * Get list user customer with filter
     *
     * @param array $params
     *
     * @return collection User
     */
    public function getListCustomerWithFilter(array $params)
    {
        return $this->model::query()
            ->filterWithParams($params)
            ->select('id', 'name', 'first_name', 'last_name', 'gender', 'created_at', 'phone', 'status')
            ->with('profile')
            ->with(['country' => function ($query) {
                $query->select('id', 'alt', 'currency', 'currency_code');
            }])
            ->isCustomer()
            ->latest('id');
    }

    /**
     * Count list user to day
     * @param string user type
     * @return number
     */
    public function countListUser()
    {
        $today = Carbon::today()->format('Y-m-d');
        return $this->model::query()
            ->selectRaw("COUNT(CASE WHEN user_type = ? THEN 1 END) AS total_customer", [User::IS_USER])
            ->selectRaw("COUNT(CASE WHEN user_type = ? THEN 1 END) AS total_worker", [User::IS_WORKER])
            ->selectRaw("COUNT(CASE WHEN user_type = ? AND worker_status = ? THEN 1 END) AS total_worker_online", [User::IS_WORKER, 1])
            ->selectRaw("COUNT(CASE WHEN user_type = ? AND is_working = ? THEN 1 END) AS total_worker_working", [User::IS_WORKER, 1])
            ->selectRaw("COUNT(CASE WHEN user_type = ? AND (DATE_FORMAT(created_at, '%Y-%m-%d')) = ? THEN 1 END) AS total_customer_today", [User::IS_USER, $today])
            ->selectRaw("COUNT(CASE WHEN user_type = ? AND (DATE_FORMAT(created_at, '%Y-%m-%d')) = ? THEN 1 END) AS total_worker_today", [User::IS_WORKER, $today])
            ->first();
    }

    /**
     * Get list user worker with filter
     *
     * @param array $params
     *
     * @return collection User
     */
    public function getListWorkerWithFilter(array $params = [])
    {
        return $this->model::query()
            ->filterWithParams($params)
            ->withAvg('ratings', 'rating')
            ->with('profile')
            ->with(['country' => function ($query) {
                $query->select('id', 'alt', 'currency', 'currency_code');
            }])
            ->isWorker()
            // ->orderBy('worker_status', 'desc')
            // ->orderBy('is_working', 'desc')
            ->orderBy(DB::raw('case when status = "2" then 3 when worker_status = "1" then 2 when is_working = "1" then 1 end'), 'desc')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get list worker name
     *
     * @param mixed $term
     *
     * @return object
     */
    public function getListWorkerName($term)
    {
        return $this->model::query()
            ->select('id', 'name as text', 'user_type')
            ->isWorker()
            ->where(function ($query) use ($term) {
                $query->where('name', 'like', '%' . $term . '%')
                    ->orWhere('id', 'like', '%' . $term . '%');
            })
            ->orderBy('name', 'asc')
            ->simplePaginate(15);
    }

    /**
     * Get list worker pending
     * @return collection User
     */
    public function listWorkerPending()
    {
        return Cache::remember(config('constant.cache.worker_pending'), config('constant.cache.time'), function () {
            return $this->model->with('profile')
                ->isWorker()
                ->isPending()
                ->latest('id')
                ->limit(8)
                ->get();
        });
    }

    /**
     * Get list account with filter
     *
     * @param array $params
     *
     * @return collection users
     */
    public function getListAccountWithFilter(array $params = [])
    {
        return $this->model::query()
            ->filterWithParams($params)
            ->with('userRole')
            ->whereNotNull('role_id')
            ->where('user_type', 'account')
            ->latest('id')
            ->get();
    }

    /**
     * Create user account
     * @param array $data
     * @return User
     */
    public function createUserAccount(array $data = [])
    {
        $roleData = explode('_', $data['role_data']);
        $user = $this->model->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'role_id' => $roleData[0],
            'gender' => $data['gender'],
            'status' => $data['status'],
            'user_type' => 'account',
            'password' => Hash::make($data['password']),
        ]);

        $user->assignRole($roleData[1]);

        return $user;
    }

    /**
     * Update user account
     * @param User $user
     * @param array $data
     * @return User
     */
    public function updateUserAccount(User $user, array $data = []): User
    {
        $roleData = explode('_', $data['role_data']);
        $roleID = $roleData[0];

        if ($user->role_id != $roleID) {
            $user->removeRole($user->userRole->name);
            $user->assignRole($roleData[1]);
        }

        $user->update([
            'name' => $data['name'],
            'role_id' => $roleID,
            'gender' => $data['gender'],
            'status' => $data['status'],
        ]);

        return $user;
    }

    /**
     * Get user detail by ID
     * @param $id
     * @return User
     */
    public function getUserDetailByID($id)
    {
        return $this->model->with('profile')->where('id', $id)->first();
    }

    /**
     * User detail
     * @param User $user
     * @return User
     */
    public function detail(User $user): User
    {
        return $user->withAvg('ratings', 'rating')
            ->with('profile')
            ->with(['tokenPayments' => function ($query) {
                $query->select('id', 'user_id', 'bank_name', 'bank_type', 'card_no', 'pay_token', 'created_at')
                    ->latest('id');
            }])
            ->with(['orders' => function ($query) {
                $query->with('detail')
                    ->where('order_status', OrderStatus::DONE);
            }])
            ->where('id', $user->id)
            ->first();
    }

    /**
     * Get list worker activity
     *
     * @param array $params
     *
     * @return mixed
     */
    public function getListWorkerActivity(array $params)
    {
        return $this->model::query()
            ->where('id', $params['user_id'])
            ->with(['orders' => function ($query) use ($params) {
                $query->filterWithParams($params)
                    ->with('detail')
                    ->where('order_status', OrderStatus::DONE)
                    ->latest();
            }])
            ->first();
    }

    /**
     * Worker info
     */
    public function getWorkerInfo(User $user)
    {
        return Order::query()
            ->selectRaw(
                "COUNT(CASE WHEN worker_id = ? AND order_status != ? THEN 1 END) * 100 
                / COUNT(CASE WHEN worker_id = ? THEN 1 END) 
                AS percent_accepted,
                COUNT(CASE WHEN worker_id = ? AND order_status = ? THEN 1 END) * 100
                / COUNT(CASE WHEN worker_id = ? THEN 1 END) 
                AS percent_canceled",
                [$user->id, OrderStatus::CANCEL, $user->id, $user->id, OrderStatus::CANCEL, $user->id]
            )
            ->first();
    }

    /**
     * Update status user
     * @param User $user
     * @param $status
     */
    public function updateUserStatus(User $user, $status)
    {
        return $user->update([
            'status' => $status
        ]);
    }
    /**
     * Reset password
     * @param User $user
     * @param $newPassword
     */
    public function resetPassword(User $user, $newPassword)
    {
        return $user->update([
            'password' => Hash::make($newPassword)
        ]);
    }

    /**
     * Remove user
     * @param User $user
     */
    public function removeUser(User $user)
    {
        return $user->user_type != User::IS_ADMIN ? $user->delete() : false;
    }

    /**
     *  Handle send otp with firebase
     * 
     * @param array $data
     *
     * @return string
     */
    public function sendOtpWithFirebase(array $data = [])
    {
        $requestTypes = array('USER_GET_OTP', 'USER_SIGNUP', 'FORGOT_PASSWORD');
        $type = $data['type'];
        $phone = str_replace('+84', '0', $data['phone']);
        if (substr($phone, 0, 2) == '00') {
            $phone = str_replace(substr($phone, 0, 2), '0', $phone);
        }
        $user = $this->model->where('phone', $data['phone'])->where('user_type', $data['user_type'])->first();

        //check neu dang ky moi ma co user thi return
        if (!in_array($type, $requestTypes) || ($type == $requestTypes[1] && !is_null($user)) || ($type == $requestTypes[2] && is_null($user))) {
            return false;
        }

        // Call api google identitytoolkit send sms otp
        $option = [
            'body' => json_encode([
                'phoneNumber' => $data['phone'],
                'recaptchaToken' => $data['recaptcha_token']
            ])
        ];
        $response = callGuzzleHttp('POST', config('constant.firebase.url_send_code'), $option);
        if (!empty($response['sessionInfo'])) {
            // Save sms firebase info
            SmsFirebaseInfo::create([
                'phone' => $data['phone'], //origin phone mobile sent
                'session_info' => $response['sessionInfo'],
                'type' => $type,
                'expired_date' => Carbon::now()->addMinutes(3),
            ]);

            return $response['sessionInfo'];
        }

        return false;
    }

    /**
     * Check stripe card exist
     * 
     * @param array $data
     * 
     * @return boolean
     */
    public function checkStripeCardExist(array $data = [])
    {
        return UserTokenPayment::query()
            ->where('user_id', $data['user_id'])
            ->where('card_brand', $data['card_brand'])
            ->where('card_no', $data['card_number'])
            ->whereRaw('DATEDIFF(CURDATE(), last_used_date) <= 165')
            ->whereNull('deleted_at')
            ->exists();
    }
     
}
