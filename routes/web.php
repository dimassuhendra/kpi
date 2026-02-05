<?php

use App\Http\Controllers\Auth\LoginController;

use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Staff\KpiController as StaffKpiController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Grup Route Dashboard (Gunakan Middleware)
Route::middleware(['auth'])->group(function () {});

Route::middleware(['auth', 'role:staff'])->group(function () {
    Route::get('/dashboard/staff', [StaffDashboardController::class, 'index'])->name('staff.dashboard');

    Route::get('/dashboard/staff/input', [StaffKpiController::class, 'create'])->name('staff.kpi.create');
    Route::post('/dashboard/staff/input', [StaffKpiController::class, 'store'])->name('staff.kpi.store');
});
