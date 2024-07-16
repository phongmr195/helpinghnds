<?php

use App\Http\Controllers\Admin\Payout\PayoutController;

Route::get('/cashout', [PayoutController::class, 'show'])->name('admin.pages.cashout');
Route::get('/ajax/get-list-cashout', [PayoutController::class, 'getListCashout'])->name('admin.ajax.list_cashout');
Route::post('/ajax/approve-cashout', [PayoutController::class, 'hanldeApproveCashout'])->name('admin.ajax.approve_cashout');
Route::post('/ajax/cancel-cashout', [PayoutController::class, 'handleCancelCashout'])->name('admin.ajax.cancel_cashout');