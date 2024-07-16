<?php

use App\Http\Controllers\Admin\Order\OrderController;

Route::group(['prefix' => 'orders'], function () {
    Route::get('/', [OrderController::class, 'index'])->name('admin.orders.list');
    Route::get('/ajax-list-order', [OrderController::class, 'getListOrder'])->name('admin.orders.ajax.list');
    Route::get('/filter', [OrderController::class, 'filterOrder'])->name('admin.orders.filter');
    Route::get('/{order}', [OrderController::class, 'showDetailOrder'])->where('order', '[0-9]+')->name('admin.orders.detail');
});