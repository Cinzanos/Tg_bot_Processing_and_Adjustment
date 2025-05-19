<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ShiftController;
use App\Http\Controllers\Admin\EquipmentController;
use App\Http\Controllers\Admin\ProcessingController;
use App\Http\Controllers\Admin\AdjustmentController;
use App\Http\Controllers\Admin\AdjustmentWaitingController;
use App\Http\Controllers\Admin\DowntimeController;
use App\Http\Controllers\Admin\RemarkController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SectionController;
use Illuminate\Support\Facades\Route;




Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/', fn() => view('admin.dashboard'))->name('dashboard');
        Route::resource('users', UserController::class);
        Route::resource('shifts', ShiftController::class);
        Route::resource('equipment', EquipmentController::class);
        Route::resource('processings', ProcessingController::class);
        Route::resource('adjustments', AdjustmentController::class);
        Route::resource('adjustment-waitings', AdjustmentWaitingController::class);
        Route::resource('downtimes', DowntimeController::class);
        Route::resource('remarks', RemarkController::class);
        Route::resource('sections', SectionController::class);
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');
    });
});
