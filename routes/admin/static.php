<?php

use App\Http\Controllers\Admin\Page\PageController;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('admin.home');
Route::get('/dashboard', [HomeController::class, 'index'])->name('admin.dashboard');
Route::get('/page-not-found', [PageController::class, 'page404'])->name('admin.pages.404');
Route::get('/page-500', [PageController::class, 'page505'])->name('admin.pages.500');
Route::get('system-logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index'])->name('admin.logs_views');
Route::group(['middleware' => ['role:admin']], function () {
    Route::get('/report', [PageController::class, 'getReport'])->name('admin.report');
});