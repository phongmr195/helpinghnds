<?php

use App\Http\Controllers\Admin\Payment\PaymentController;

Route::group(['prefix' => 'payment', 'middleware' => 'auth:api'], function () {
    Route::get('/add-card', [PaymentController::class, 'getUrlViewAddCard']);
    Route::get('/payment-with-atm', [PaymentController::class, 'getUrlViewPaymentATM']);
});