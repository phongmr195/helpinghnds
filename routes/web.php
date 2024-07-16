<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Web public route
Route::group([], function () {
    includeRouteFiles(__DIR__ . '/public/');
});

Auth::routes();
Route::redirect('/', '/admin/dashboard', 301);
// Web admin
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'is_admin', 'lock_screen']], function () {
    includeRouteFiles(__DIR__ . '/admin/');
});
