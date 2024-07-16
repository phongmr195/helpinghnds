<?php 

use App\Http\Controllers\Admin\User\UserController;
use App\Http\Controllers\Admin\Payment\PaymentController;

Route::group(['prefix' => 'users'], function () {
    // All user access to route
    Route::get('/', [UserController::class, 'index'])->name('admin.users.list');
    Route::get('/account', [UserController::class, 'listAccount'])->name('admin.users.list-account')->middleware('is_admin_root');
    Route::get('/account/filter', [UserController::class, 'listAccount'])->name('admin.users.filter_list_account')->middleware('is_admin_root');
    Route::get('/ajax/account', [UserController::class, 'getListAccount'])->name('admin.users.ajax.list_account');
    Route::get('/ajax/get-data-user-account', [UserController::class, 'getDataUserAccount'])->name('admin.users.ajax.data_user_account');
    Route::get('/customer', [UserController::class, 'listCustomer'])->name('admin.users.list-customer');
    Route::get('/customer/{user}', [UserController::class, 'detail'])->where('user', '[0-9]+')->name('admin.users.customer-detail');
    Route::get('/customer/filter', [UserController::class, 'filterCustomer'])->name('admin.users.filter-customer');
    Route::get('/ajax/list-customer', [UserController::class, 'getListCustomer'])->name('admin.users.ajax.list_customer');
    Route::get('/worker', [UserController::class, 'listWorker'])->name('admin.users.list-worker');
    Route::get('/worker/{user}', [UserController::class, 'detail'])->where('user', '[0-9]+')->name('admin.users.worker-detail');
    Route::get('/worker/filter', [UserController::class, 'filterWorker'])->name('admin.users.filter-worker');
    Route::get('/ajax/list-worker', [UserController::class, 'getListWorker'])->name('admin.users.ajax.list_worker');
    Route::post('/ajax/push-to-call-user', [UserController::class, 'pushToCallUser'])->name('admin.users.ajax.pushToCallUser');
    Route::get('/ajax/get-list-worker-activity', [UserController::class, 'getListWorkerActivity'])->name('admin.users.ajax.list_worker_activity');
    Route::post('/ajax/filter-total-earned', [UserController::class, 'filterTotalEarned'])->name('admin.users.ajax.filter_total_earned');
    Route::get('/ajax/get-list-worker-name', [PaymentController::class, 'getListWorkerName'])->name('admin.users.ajax.list_worker_name');

    // Only user has role root access permission to routes
    Route::group(['middleware' => 'is_admin_root'], function(){
        Route::post('/{user}/update', [UserController::class, 'updateUser'])->where('user', '[0-9]+')->name('admin.users.update');
        Route::post('/{user}/delete', [UserController::class, 'removeUser'])->where('user', '[0-9]+')->name('admin.users.delete');
        Route::post('/{user}/update-status', [UserController::class, 'updateUserStatus'])->where('user', '[0-9]+')->name('admin.users.update-status');
        Route::post('/{user}/reset-password', [UserController::class, 'resetPassword'])->where('user', '[0-9]+')->name('admin.users.reset-password');
        Route::post('/create-account', [UserController::class, 'createAccount'])->name('admin.users.create-account');
        Route::post('/{user}/update-account', [UserController::class, 'updateUserAccount'])->where('user', '[0-9]+')->name('admin.users.update-account');
    });
});
