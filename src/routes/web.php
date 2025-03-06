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

// 一般ユーザー
Route::middleware('auth')->group(function () {
    Route::get('/attendance', [UserController::class, 'attendance']);
    Route::post('/attendance', [UserController::class, 'attendanceStore']);
    
    Route::get('/attendance/list/{year?}/{month?}', [UserController::class, 'attendanceList'])->name('attendance.list');

    Route::get('/attendance/{id}', [UserController::class, 'attendanceDetail'])->name('attendance.detail');
    Route::post('/attendance/{id}', [UserController::class, 'attendanceDetailRequest']);

    Route::get('/stamp_correction_request/list', [UserController::class, 'correctionRequestList']);
});

Route::get('/admin/login', [AdminController::class, 'showAdminLogin']);
Route::post('/admin/login', [AdminController::class, 'adminLogin']);
    
// 管理者
Route::middleware(['auth:admin'])->group(function () {
    Route::get('/admin/attendance/list/{year?}/{month?}/{day?}', [AdminController::class, 'adminAttendanceList'])->name('admin.attendance.list');

    Route::get('/admin/attendance/{id}', [AdminController::class, 'adminAttendanceDetail'])->name('admin.attendance.detail');
    Route::post('/admin/attendance/{id}', [AdminController::class, 'adminAttendanceDetailUpdate']);

    Route::get('/admin/staff/list', [AdminController::class, 'adminStaffList']);

    Route::get('/admin/attendance/staff/{id}/{year?}/{month?}', [AdminController::class, 'adminAttendanceStaff'])->name('admin.attendance.staff');

    Route::get('/admin/stamp_correction_request/list', [AdminController::class, 'adminCorrectionRequestList']);

    Route::get('/admin/stamp_correction_request/approve/{request_id}', [AdminController::class, 'adminApproval']);
    Route::post('/admin/stamp_correction_request/approve/{request_id}', [AdminController::class, 'adminApprovalUpdate']);
});