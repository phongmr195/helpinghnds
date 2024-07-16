<?php

use App\Http\Controllers\Api\Order\OrderController;
use App\Http\Controllers\Api\User\UserController;

// User
Route::group(['prefix' => 'users'], function () {
    Route::post('/signup/worker', [UserController::class, 'registerWorker']);
    Route::post('/signup/user', [UserController::class, 'registerUser']);
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/send-otp', [UserController::class, 'sendOtp'])->name('api.users.send-otp');
    Route::post('/verify-otp', [UserController::class, 'verifyOtp'])->name('api.users.verify-otp');
    Route::post('/forgot-pass', [UserController::class, 'forgotPass'])->name('users.forgot-pass');
    Route::post('/check-user-signuped', [UserController::class, 'checkUserSignuped'])->name('api.users.check-signuped');
    Route::post('/delete-token-card', [UserController::class, 'deleteTokenCard'])->name('api.delete_card')->withoutMiddleware('json');
    Route::get('/get-config-iceservers', [UserController::class, 'getICEServers'])->name('users.getICEServers');
    Route::group(['middleware' => 'auth:api'], function () {
        Route::post('/refresh-token', [UserController::class, 'refreshToken'])->name('api.users.refresh-token');
        Route::post('/check-logged', [UserController::class, 'checkLogged'])->name('users.check-logged');
        Route::get('/user', [UserController::class, 'getUserDetail'])->name('api.users.detail');
        Route::post('/user/update-location', [UserController::class, 'updateUserLocation'])->name('api.users.update');
        Route::get('/workers', [UserController::class, 'getListWorker'])->name('api.users.list_worker');
        Route::post('/worker/update-status', [UserController::class, 'updateWorkerStatus'])->name('api.users.worker.update-status');
        Route::get('/worker/ask-work', [OrderController::class, 'checkHasOrder'])->name('api.users.worker.get-job');
        Route::post('/worker/accept-work', [OrderController::class, 'workerAcceptWork'])->name('api.users.worker.accept-work');
        Route::get('/list-location', [UserController::class, 'getListLocation'])->name('api.users.location');
        Route::post('/user/change-pass', [UserController::class, 'changePass'])->name('api.users.change-pass');
        Route::post('/user/update-profile', [UserController::class, 'updateProfile'])->name('users.update-profile');
        Route::post('/user/call', [UserController::class, 'call'])->name('users.call');
        Route::get('/worker/revenue', [UserController::class, 'getRevenueByWorker'])->name('users.worker.revenue');
        Route::get('/list-card', [UserController::class, 'getListCard'])->name('users.list_card');
        Route::post('/verify-token-card', [UserController::class, 'verifyTokenCard'])->name('users.verify_token_card');
        Route::post('/worker-push-user-done-job', [OrderController::class, 'workerPushDoneJob'])->name('users.worker_push_done_job');
    });
});
