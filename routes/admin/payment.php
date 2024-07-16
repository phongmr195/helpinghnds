<?php

use App\Http\Controllers\Admin\Payment\PaymentController;

Route::get('/payment', [PaymentController::class, 'showListPayment'])->name('admin.payment');
Route::get('/ajax/get-list-payments', [PaymentController::class, 'getListPaymentWithFilter'])->name('admin.payment.ajax.list_payments');
Route::post('/ajax/getTotalCashInCashOut', [PaymentController::class, 'getTotalCashInCashOut'])->name('admin.payment.ajax.total_cashIn_cashOut');