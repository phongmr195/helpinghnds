<?php

use App\Http\Controllers\Api\Service\ServiceController;

Route::group(['prefix' => 'services', 'middleware' => 'auth:api'], function () {
    Route::get('/list', [ServiceController::class, 'getListService'])->name('api.services.list');
});
