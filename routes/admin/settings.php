<?php

use App\Http\Controllers\Admin\Setting\SettingController;

Route::group(['prefix' => 'settings', 'middleware' => ['role:admin']], function () {
    Route::get('/', [SettingController::class, 'index'])->name('admin.settings');
    Route::get('/roles/add', [SettingController::class, 'viewAdd'])->name('admin.settings.view_add');
    Route::get('/roles/{id}', [SettingController::class, 'detail'])->where('id', '[0-9]+')->name('admin.settings.detail-role');
    Route::get('/roles/{id}/edit', [SettingController::class, 'viewEditRole'])->where('id', '[0-9]+')->name('admin.settings.view-edit-role');
    Route::post('/roles/edit', [SettingController::class, 'editRole'])->name('admin.settings.edit-role');
    Route::post('/roles/ajax-data-edit-role', [SettingController::class, 'getDataEditRole'])->name('admin.settings.ajax-data-edit-role');
    Route::post('/roles/add-role', [SettingController::class, 'addRole'])->name('admin.settings.add-role');
});