<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;

use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Staff\KpiController as StaffKpiController;
use App\Http\Controllers\Staff\PerformanceController as StaffPerformanceController;
use App\Http\Controllers\Staff\ProfileController as StaffProfileController;

use App\Http\Controllers\Manager\DashboardController;
use App\Http\Controllers\Manager\ApprovalController;
use App\Http\Controllers\Manager\AnalyticsController;
use App\Http\Controllers\Manager\VariableController;
use App\Http\Controllers\Manager\ReportController;
use App\Http\Controllers\Manager\StaffController;

use Illuminate\Support\Facades\Route;

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// Grup Route Dashboard (Gunakan Middleware)
Route::middleware(['auth', 'role:staff'])->group(function () {
    Route::get('/dashboard/staff', [StaffDashboardController::class, 'index'])->name('staff.dashboard');

});

Route::middleware(['auth', 'role:manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

});
