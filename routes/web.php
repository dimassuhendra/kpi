<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;

use App\Http\Controllers\StaffKpiController;

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
Route::group(['middleware' => ['auth', 'role:staff'], 'prefix' => 'staff'], function () {
    Route::get('/dashboard', [StaffKpiController::class, 'dashboard'])->name('staff.dashboard');
    Route::get('/input-case', [StaffKpiController::class, 'index'])->name('staff.input');
    Route::post('/kpi/store', [StaffKpiController::class, 'store'])->name('staff.kpi.store');
    Route::get('/kpi/logs', [StaffKpiController::class, 'logs'])->name('staff.kpi.logs');
    Route::put('/kpi/logs/{id}', [StaffKpiController::class, 'update'])->name('staff.kpi.update');
    Route::delete('/kpi/logs/{id}', [StaffKpiController::class, 'destroy'])->name('staff.kpi.destroy');
    Route::put('/kpi/case-update/{id}', [StaffKpiController::class, 'updateCase'])->name('staff.kpi.case_update');
    Route::get('/kpi/export-excel', [StaffKpiController::class, 'exportExcel'])->name('staff.kpi.export.excel');
    Route::get('/kpi/achievements', [StaffKpiController::class, 'achievements'])->name('staff.kpi.achievements');
});

Route::middleware(['auth', 'role:manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});
