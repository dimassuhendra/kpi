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

    Route::get('/dashboard/staff/input', [StaffKpiController::class, 'create'])->name('staff.kpi.create');
    Route::post('/dashboard/staff/input', [StaffKpiController::class, 'store'])->name('staff.kpi.store');

    Route::get('/dashboard/staff/history', [StaffKpiController::class, 'history'])->name('staff.kpi.history');

    Route::get('/dashboard/staff/performance', [StaffPerformanceController::class, 'index'])->name('staff.performance');

    Route::get('/profile', [StaffProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [StaffProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [StaffProfileController::class, 'updatePassword'])->name('profile.password');
});

Route::middleware(['auth', 'role:manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/approval', [ApprovalController::class, 'index'])->name('approval.index');
    Route::post('/approval/{id}/process', [ApprovalController::class, 'process'])->name('approval.process');

    // Route lain (opsional jika ingin dipisah)
    Route::get('/approval-history', [ApprovalController::class, 'history'])->name('approval.history');
});
