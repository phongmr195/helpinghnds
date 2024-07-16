<?php
use App\Http\Controllers\Api\Hrms\HrmsController;

Route::get('/hrms/kpi', [HrmsController::class, 'kpi'])->name('api.hrms.kpi');
Route::get('/hrms/reviews', [HrmsController::class, 'performanceReviews'])->name('api.hrms.performanceReviews');
Route::post('/hrms/reviews', [HrmsController::class, 'postPerformanceReviews'])->name('api.hrms.postPerformanceReviews');