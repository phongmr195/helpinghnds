<?php

use App\Http\Controllers\Api\PageStatic\StaticController;

Route::group(['prefix' => 'google'], function() {
    Route::get('/geocoding', [StaticController::class, 'googleGeoCoding']);
});

Route::group(['prefix' => 'upload'], function() {
    Route::post('/image', [StaticController::class, 'uploadImage'])->name('api.static.upload-image');
    Route::post('/images', [StaticController::class, 'uploadMultiImage'])->name('api.static.upload-multi-image');
});

Route::post('/demo-push-noti', [StaticController::class, 'demoPushNoti'])->name('demo.push-noti');