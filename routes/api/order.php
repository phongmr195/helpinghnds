<?php

use App\Http\Controllers\Api\Order\OrderController;

Route::group(['prefix' => 'orders', 'middleware' => 'auth:api'], function(){
    Route::get('/{order}/status', [OrderController::class, 'getStatusOrder'])->where('order', '[0-9]+')->name('api.oders.status');
    Route::post('/create', [OrderController::class, 'createOrder'])->name('api.orders.create');
    Route::post('/{order}/confirm', [OrderController::class, 'confirmOrder'])->where('order', '[0-9]+')->name('api.orders.confirm');
    Route::post('/{order}/start-or-cancel', [OrderController::class, 'startOrCancelOrder'])->where('order', '[0-9]+')->name('api.orders.start_or_cancel');
    Route::post('/{order}/pause', [OrderController::class, 'pauseOrder'])->where('', '[0-9]+')->name('api.orders.pause');
    Route::post('/{order}/payment', [OrderController::class, 'paymentOrder'])->where('oorderrder', '[0-9]+')->name('api.orders.payment');
    Route::post('/check-status-order', [OrderController::class, 'checkStatusOrder'])->name('api.orders.check-status');
    Route::post('/order/worker-arrived',[OrderController::class, 'workerArrive'])->name('api.orders.worker-arrived');
    Route::post('/order/start-work', [OrderController::class, 'orderStartWork'])->name('api.orders.order-start-work');
    Route::post('/order/worker/pause-or-resum-work', [OrderController::class, 'workerPauseOrResumWork'])->name('api.orders.wroker-pause-or-resum-work');
    Route::post('/order/user/pause-or-resum-work', [OrderController::class, 'userPauseOrResumWork'])->name('api.orders.user-pause-or-resum-work');
    Route::post('/order/finish-work', [OrderController::class, 'orderFinishWork'])->name('api.orders.order-finish-work');
    Route::post('/order/current-worker-location', [OrderController::class, 'getOrderWorkerLocation'])->name('api.orders.order-worker-location');
    Route::post('/order/detail', [OrderController::class, 'getDetailByUserOrWorker']);
    Route::post('/order/push-incoming', [OrderController::class, 'pushIncoming']);
    Route::get('/user/list', [OrderController::class, 'getOrdersByUserID']);
    Route::post('/order/user/tip-for-helper', [OrderController::class, 'tipOrderByUser']);
    Route::post('/order/user/re-payment', [OrderController::class, 'rePaymentOrder']);
    Route::get('/order-non-paid', [OrderController::class, 'getOrdersNonpaid'])->name('api.orders.non_paid');
});
