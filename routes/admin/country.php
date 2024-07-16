<?php

use App\Http\Controllers\Admin\Country\CountryController;

Route::group(['prefix' => 'countries'], function () {
    Route::get('/', [CountryController::class, 'index'])->name('admin.countries.list');
    Route::get('/add', [CountryController::class, 'viewAdd'])->name('admin.countries.view_add');
    Route::get('/{country}', [CountryController::class, 'detail'])->where('country', '[0-9]+')->name('admin.countries.view_detail');
    Route::post('/{country}/edit', [CountryController::class, 'editCountry'])->where('country', '[0-9]+')->name('admin.countries.edit');
    Route::post('/add-country', [CountryController::class, 'addCountry'])->name('admin.countries.add');
});