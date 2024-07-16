<?php

namespace App\Http\Controllers\Admin\Firebase;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use App\Services\Admin\UserService;
use App\Http\Requests\Api\SendOtpRequest;

class FirebaseController extends Controller
{
    use ApiResponser;
    protected $userService;
    
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }


    /**
     * Send SMS OTP
     */
    public function sendOtp(SendOtpRequest $request)
    {
        $data = $request->all();
        $data['user_type'] = $request->header('UserType', '');

        $response = $this->userService->sendOtpWithFirebase($data);

        if ($response) {
            return $this->success($response);
        }

        return $this->success(false);
    }
}
