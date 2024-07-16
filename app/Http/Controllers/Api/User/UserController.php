<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Api\RegisterWorkerRequest;
use App\Http\Requests\Api\LoginUserRequest;
use App\Http\Requests\Api\UpdateLocationRequest;
use App\Http\Requests\Api\RegisterUserRequest;
use App\Http\Requests\Api\SendOtpRequest;
use App\Http\Requests\Api\VerifyOtpRequest;
use App\Http\Requests\Api\CheckUserRegisteredRequest;
use App\Http\Requests\Api\UpdateWorkerStatusRequest;
use App\Http\Requests\Api\User\ChangePassRequest;
use App\Http\Requests\Api\User\ForgotPassRequest;
use App\Http\Requests\Api\Payment\DeleteCardRequest;
use App\Http\Requests\Api\Payment\VerifyTokenCardRequest;
use App\Models\User;
use App\Traits\ApiResponser;
use App\Traits\ImageUpload;
use App\Services\Api\UserService;
use App\Services\Api\FcmService;
use App\Services\Admin\PaymentService;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    use ApiResponser, ImageUpload;
    protected $userService;
    protected $paymentService;

    public function __construct(UserService $userService, PaymentService $paymentService)
    {
        $this->userService = $userService;
        $this->paymentService = $paymentService;
    }

    function getICEServers()
    {
        $iceGoogle = array(
            'iceServers' => array(
                array(
                    'url' => 'stun:stun.l.google.com:19302',
                    'urls' => ['stun:stun.l.google.com:19302']
                ),
                array(
                    'url' => 'turn:turn-02.giacongphanmem.vn:7479?transport=tcp',
                    'urls' => ['turn:turn-02.giacongphanmem.vn:7479?transport=tcp'],
		            'username' => 'assist01',
                    'credential' => 'assistMe@202308'                    
                ),
                array(
                    'url' => 'turn:turn-02.giacongphanmem.vn:7478?transport=udp',
                    'urls' => ['turn:turn-02.giacongphanmem.vn:7478?transport=udp'],
		            'username' => 'assist01',
                    'credential' => 'assistMe@202308'                    
                )
                // array(
                //     'url' => 'turn:13.57.42.200:3478',
                //     'urls' => ['turn:13.57.42.200:3478'],
		        //     'username' => 'assist01',
                //     'credential' => 'assistMe@202308'                    
                // ) 
            )
        );
        $config = array(
            'servers' => $iceGoogle,
            // 'sessions' => array(
            //     'sessionConstraints' => array(
            //         'mandatory' => array(
            //             'OfferToReceiveAudio' => true,
            //             'OfferToReceiveVideo' => true,
            //             'VoiceActivityDetection' => true
            //         )
            //     )
            // )
        );
        return $this->success($config);
    }

    public function registerUser(RegisterUserRequest $request)
    {
        $data = $request->all();
        $data['device_token'] = $request->header('DeviceToken', '');
        $user = $this->userService->createUser($data);
        $userData = [
            'user_data' => getUserDataObject($user->id)
        ];
        return $this->success($userData);
    }

    public function registerWorker(RegisterWorkerRequest $request)
    {
        $data = $request->all();
        $data['device_token'] = $request->header('DeviceToken', '');
        $worker = $this->userService->createWorker($data);
        $userData = [
            'user_data' => getUserDataObject($worker->id)
        ];
        return $this->success($userData);
    }


    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @return [string] access_token
     * @return [string] expires_at
     */
    public function login(LoginUserRequest $request)
    {
        $user = $this->userService->login($request);

        return !$user ? $this->error(User::NONE_USER_MESSAGE, 401, null) : $this->success($user);
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        $request->headers->set('access_token_user', null);
        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh token
     */
    public function refreshToken(Request $request)
    {
        $tokenResponse = $this->userService->refreshToken($request);
        return $this->success($tokenResponse);
    }

    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function getUserDetail(Request $request)
    {
        $userData = [
            'user_detail_data' => getUserDetailDataObject($request->user()->id)
        ];
        return $this->success($userData);
    }

    public function updateUserLocation(UpdateLocationRequest $request)
    {
        $this->userService->updateUserLocation($request->user(), $request->all());
        return $this->success(null);
    }

    /**
     * Update worker status
     */
    public function updateWorkerStatus(UpdateWorkerStatusRequest $request)
    {
        $data = $request->all();
        $data['device_token'] = $request->header('deviceToken', '');
        $workerUpdate = $this->userService->updateWorkerStatus($request->user(), $data);

        if ($workerUpdate) {
            return $this->success($workerUpdate);
        }

        return $this->success(null, 'User is not worker!', 200);
    }

    /**
     * Get list worker for user
     * @return [json] worker object
     */
    public function getListWorker(Request $request)
    {
        $user = $request->user();
        $workers = [];
        if (!empty($user) && !empty($request->latitude) && !empty($request->longtitude)) {
            $workers = $this->userService->getListWorker($request->latitude, $request->longtitude, $user->nation_code);
        }
        return $this->success($workers);
    }

    /**
     * Send SMS OTP
     */
    public function sendOtp(SendOtpRequest $request)
    {
        $data = $request->all();
        $data['user_type'] = $request->header('UserType', '');

        $response = $this->userService->sendOtp($data);

        if ($response) {
            return $this->success($response);
        }

        return $this->success(false);

        // return response()->json([
        //     "code" =>  400,
        //     "message" => "CAPTCHA_CHECK_FAILED : Recaptcha verification failed - MALFORMED",
        //     "data" => null
        // ], 400);
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(VerifyOtpRequest $request)
    {
        $data = $request->all();
        $data['user_type'] = $request->header('UserType', '');
        $response = $this->userService->verifyOtp($data);
        if ($response) {
            return $this->success(true, 'Verify otp success!');
        }

        return $this->badRequest(400, 'Bad Request', 'Invalid otp or expired time!');
    }

    /**
     * Forgot pass
     */
    public function forgotPass(ForgotPassRequest $request)
    {
        $data = $request->all();
        $data['user_type'] = $request->header('UserType', '');
        $response = $this->userService->forgotPass($data);
        if ($response) {
            return $this->success(true);
        }

        return $this->badRequest(400, 'Bad Request', 'Invalid otp!');
    }

    /**
     * List location
     */
    public function getListLocation(Request $request)
    {
        $locations = $this->userService->getListLocation($request->user());
        return $this->success($locations);
    }

    /**
     * Check user signuped
     */
    public function checkUserSignuped(CheckUserRegisteredRequest $request)
    {
        $user = $this->userService->checkUserSignuped($request->all());
        return $this->success($user);
    }

    /**
     * Change pass
     */
    public function changePass(ChangePassRequest $request)
    {
        $changePass = $this->userService->changePass($request->user(), $request->password_old, $request->password);
        if ($changePass) {
            return $this->success($changePass);
        }

        return $this->badRequest(400, 'Bad request', 'Password invalid');
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        $update = $this->userService->updateProfile($request->user(), $request->all());
        return $this->success($update);
    }

    /**
     * Check user logged
     */
    public function checkLogged(Request $request)
    {
        $check = $this->userService->checkLogged($request->user());
        if ($check) {

            //cap nhat thong tin user khi reopen app
            $user = $request->user();
            $user->update([
                'device_token' => $request->header('DeviceToken', null) ?? $user->device_token,
                'device_platform' => $request->header('DevicePlatform', null) ?? $user->device_platform,
                'app_version' => $request->header('AppVersion', null) ?? $user->app_version
            ]);

            return $this->success(true);
        }

        return $this->error('Unauthorized', 401, null);
    }

    /**
     * Worker get my revenue
     * By workerId
     */
    public function getRevenueByWorker(Request $request)
    {
        $workerLogged = $request->user();

        ///format: m/d/Y
        // format m-d-Y -->reformat replace ---m/d/Y
        $fromDate = !empty($request->fromDate) ? date('Y-m-d', strtotime(str_replace('-', '/', $request->fromDate))) : date('Y-m-d', strtotime('monday this week'));
        $toDate = !empty($request->toDate) ? date('Y-m-d', strtotime(str_replace('-', '/', $request->toDate))) : date('Y-m-d', strtotime('sunday this week'));

        $queryResult = DB::select('call spc_worker_revenue(?, ?, ?)', [$workerLogged->id, $fromDate, $toDate]);
        if (collect($queryResult)) {
            $dataResult = array(
                'worker_id' => $workerLogged->id,
                'balance' => $workerLogged->balance,
                'filterData' => array(
                    'totalAmount' => $queryResult[0]->totalAmount ?? 0,
                    'totalTip' => $queryResult[0]->totalTip ?? 0,
                    'totalWorkingMinute' => $queryResult[0]->totalWorkingMinute ?? 0,
                    'totalJob' => $queryResult[0]->totalJob ?? 0,
                    'fromDate' => $fromDate,
                    'toDate' => $toDate
                )
            );
            return $this->success($dataResult);
        }

        return $this->error('Unauthorized', 401, null);
    }

    /**
     * User call to user p2p using webrtc and push notification
     */
    function call(Request $request, FcmService $fcmService)
    {
        $toUserID = $request->toUserId;
        $toUser = $this->userService->getUserByID($toUserID);
        $type = $request->type;
        $pushData = $request->pushData;
        if ($toUserID && $toUser && $type && $pushData) {

            //user request api
            $userLogged = $request->user();

            switch ($type) {
                case 'offer': //userLogged calling toUser
                    # code...
                    break;

                default:
                    # code...
                    break;
            }

            //push to call
            $notiData = [
                'title' => 'Incoming call',
                'body' => $userLogged->name . ' is calling you.',
                'image' => asset('assets/images/icon-app-for-client.jpg'),
                'fcm' => [
                    'type' => 'FCM-CALL-OFFER'
                ],
                'data' => $pushData
            ];

            $fcmService->sendNotification($toUser->device_token, $notiData);

            $data = array(
                'fromUser' => $userLogged,
                'toUser' => $toUser,
                'pushData' => $pushData
            );
            return $this->success($data);
        }

        return $this->error('Unauthorized', 401, null);
    }

    /**
     * Get list card
     */
    public function getListCard(Request $request)
    {
        $data = $this->userService->getListCard($request->user()->id);

        return $this->success($data);
    }

    /**
     * Delete token payment card
     */
    public function deleteTokenCard(DeleteCardRequest $request)
    {
        $data = $this->userService->deleteTokenCardByID($request->card_id);

        return $this->success($data);
    }

    /**
     * Verify token card
     */
    public function verifyTokenCard(VerifyTokenCardRequest $request)
    {
        $data = $this->paymentService->verifyPayToken($request->pay_token, $request->user()->id);

        return $this->success($data);
    }

    /**
     * Send mail contact us
     */
    public function sendMailContactUs(Request $request)
    {
        $user = getUserByAccessToken($request->accessToken);

        if (is_null($user)) {
            return $this->error('accessToken invalid!', 400);
        }

        $params = [
            'title' => $request->title,
            'content' => $request->content,
            'phone' => $user->phone,
            'name' => $user->name,
        ];

        $data = $this->userService->sendMailContactUs($params, $user);

        return $this->success(true);
    }
}
