<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;

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

Route::middleware('auth')->group(function () {
    Route::get('/attendance', [UserController::class, 'attendance']);
    Route::post('/attendance', [UserController::class, 'attendanceStore']);
    
    Route::get('/attendance/list/{year?}/{month?}', [UserController::class, 'attendanceList'])->name('attendance.list');

    Route::get('/attendance/{id}', [UserController::class, 'attendanceDetail']);
    Route::post('/attendance/{id}', [UserController::class, 'attendanceDetailRequest']);
});

Route::get('/admin/login', [AdminController::class, 'showAdminLogin']);
Route::post('/admin/login', [AdminController::class, 'adminLogin']);
    
Route::middleware(['auth:admin'])->group(function () {
    Route::get('/admin/attendance/list', [AdminController::class, 'adminAttendanceList']);
});