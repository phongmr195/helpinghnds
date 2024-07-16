<?php
use App\Http\Controllers\Api\Country\CountryController;

Route::get('/countries', [CountryController::class, 'getListCountry'])->name('api.country.list');