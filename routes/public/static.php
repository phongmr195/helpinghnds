<?php
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\Admin\Payment\PaymentController;
use App\Http\Controllers\Admin\Payout\PayoutController;
use App\Http\Controllers\Api\Order\OrderController;
use App\Http\Controllers\Api\User\UserController;
use App\Http\Controllers\Admin\Firebase\FirebaseController;

//Public route
Route::get('/terms-of-service', [PublicController::class, 'termsOfService']);
Route::get('/test-queues', [PublicController::class, 'testQueue']);
Route::get('/contact-us', [PublicController::class, 'contactUs']);
Route::post('/send-mail-contact-us', [UserController::class, 'sendMailContactUs'])->name('users.mail_contact_us');

// Payment with vnpt, stripe
Route::group(['prefix' => 'payment'], function () {
    Route::get('/alert', [PaymentController::class, 'showAlert'])->name('admin.payment.alert');
    Route::post('/handle-payment-done-with-3ds', [PaymentController::class, 'handlePaymentDoneWith3DS'])->name('admin.payment.handle_payment_done_3ds');
    Route::get('/add-card', [PaymentController::class, 'showViewAddCard'])->name('admin.payment.add_card');
    Route::get('/stripe/add-card', [PaymentController::class, 'showViewAddCardStripe'])->name('admin.payment.add_card_stripe');
    Route::post('/stripe/handle-add-card', [PaymentController::class, 'hanldeAddCardStripe'])->name('admin.stripe.add_card');
    Route::get('/stripe/pay', [PaymentController::class, 'showViewPaymentWithStripe'])->name('admin.payment.stripe.view');
    Route::post('/stripe/handle-payment-with-stripe', [PaymentController::class, 'handlePaymentWithStripe'])->name('admin.payment.stripe.pay');
    Route::get('/callback', [PaymentController::class, 'handleCallback'])->name('admin.payment.callback');
    Route::get('/callback-payment-with-atm', [PaymentController::class, 'handleCallbackPaymentWithATM'])->name('admin.payment.callback_payment_with_atm');
    Route::post('/notify-add-card', [PaymentController::class, 'handleNotifyAddCard'])->name('notify.payment_addcard');
    Route::get('/payment-with-atm', [PaymentController::class, 'showViewPaymentWithATM'])->name('admin.payment.atm');
    Route::post('/notify-payment-with-atm', [OrderController::class, 'handleNotifyPaymentWithATM'])->name('notify.payment_with_atm');
    Route::post('/verify-notify-add-card', [PaymentController::class, 'verifyNotifyAddCard'])->name('notify.payment_verify_addcard');
    Route::get('/notify-payment-result', [PaymentController::class, 'handleNotifyPaymentResult'])->name('notify.payment_result');
});

// Render recaptcha
Route::get('/render-recaptcha', function(){
    return view('admin.pages.recaptcha');
});
Route::post('/send-otp-with-firebase', [FirebaseController::class, 'sendOtp'])->name('api.users.web.send-otp');


// Payout for worker
Route::group(['prefix' => 'payout'], function () {
    Route::get('/of-worker', [PayoutController::class, 'ofWorker'])->name('admin.payout.of_worker');
    Route::post('/add-card-worker', [PayoutController::class, 'workerAddCard'])->name('admin.worker.add_card');
    Route::post('/get-payout-data', [PayoutController::class, 'getConfirmPayoutData'])->name('admin.confirm_payout');
    Route::post('/cash-out-for-worker', [PayoutController::class, 'cashOutForWorker'])->name('admin.worker.cash_out');
});

// Lock screen
Route::get('/lock-screen', [LoginController::class, 'locked'])->name('locked');
Route::post('/lock-screen', [LoginController::class, 'unlock'])->name('unlock');