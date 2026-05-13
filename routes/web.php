<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UpdateController;

use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Staff\ProfileController as StaffProfileController;
use App\Http\Controllers\Staff\AchievementController as StaffAchievementController;
use App\Http\Controllers\Staff\LogsController as StaffLogsController;
use App\Http\Controllers\Staff\InputController as StaffInputController;

use App\Http\Controllers\Manager\DashboardController;
use App\Http\Controllers\Manager\ValidationController;
use App\Http\Controllers\Manager\ReportController;
use App\Http\Controllers\Manager\UserController;
use App\Http\Controllers\Manager\ManagerProfileController;
use App\Http\Controllers\Manager\StorageController;

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');
Route::get('/system-updates', [UpdateController::class, 'index'])->name('updates.index');

// Grup Route Dashboard (Gunakan Middleware)
Route::group(['middleware' => ['auth', 'role:staff'], 'prefix' => 'staff'], function () {
    Route::get('/dashboard', [StaffDashboardController::class, 'dashboard'])->name('staff.dashboard');
    Route::get('/input-case', [StaffInputController::class, 'index'])->name('staff.input');
    Route::post('/upload-async', [StaffInputController::class, 'uploadAsync'])->name('staff.upload.async');
    Route::post('/kpi/store', [StaffInputController::class, 'store'])->name('staff.kpi.store');
    Route::post('/staff/feedback', [StaffInputController::class, 'storeFeedback'])->name('staff.feedback.store');
    Route::post('/staff/assessment', [StaffInputController::class, 'storeAssessment'])->name('staff.assessment.store');

    Route::get('/kpi/logs', [StaffLogsController::class, 'logs'])->name('staff.kpi.logs');
    Route::put('/kpi/logs/{id}', [StaffLogsController::class, 'update'])->name('staff.kpi.update');
    Route::delete('/kpi/logs/{id}', [StaffLogsController::class, 'destroy'])->name('staff.kpi.destroy');
    Route::delete('/staff/kpi/case-destroy/{id}', [StaffLogsController::class, 'destroyCase'])->name('staff.kpi.case-destroy');
    Route::put('/kpi/case-update/{id}', [StaffLogsController::class, 'updateCase'])->name('staff.kpi.case_update');
    Route::get('/logs/export/excel', [StaffLogsController::class, 'exportExcel'])->name('staff.logs.export.excel');

    Route::get('/kpi/achievements', [StaffAchievementController::class, 'achievements'])->name('staff.kpi.achievements');

    Route::get('/profile', [StaffProfileController::class, 'editProfile'])->name('staff.profile.edit');
    Route::put('/profile', [StaffProfileController::class, 'updateProfile'])->name('staff.profile.update');
});

Route::middleware(['auth', 'role:manager,gm'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    Route::get('/validation', [ValidationController::class, 'validationIndex'])->name('approval.index');
    Route::get('/validation/{id}', [ValidationController::class, 'validationShow'])->name('approval.show');
    Route::post('/manager/validation/{id}/update', [ValidationController::class, 'validationUpdate'])->name('manager.validation.update');
    Route::post('/validation/store', [ValidationController::class, 'validationStore'])->name('approval.store');
    Route::post('/manager/validation/assessment', [ValidationController::class, 'storeAssessment'])->name('assessment.store');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/preview', [ReportController::class, 'preview'])->name('reports.preview');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    Route::get('/manager/reports/export-divisi', [ReportController::class, 'exportDivisi'])->name('reports.export.divisi');
    Route::post('/export-pdf', [ReportController::class, 'exportPdf'])->name('export.pdf');
    Route::delete('/reports/destroy-range', [ReportController::class, 'destroyRange'])->name('reports.destroy');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/export-pdf', [UserController::class, 'exportPdf'])->name('export.pdf');
    Route::get('/export-all-staff', [UserController::class, 'exportAll'])->name('export.all');

    // Modul Storage Management
    Route::get('/storage-management', [StorageController::class, 'index'])->name('storage.index');
    Route::post('/storage-management/bulk-delete', [StorageController::class, 'bulkDestroy'])->name('storage.bulk_delete');
    Route::delete('/storage-management/{type}/{id}', [StorageController::class, 'destroy'])->name('storage.destroy');


    Route::get('/profile', [ManagerProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ManagerProfileController::class, 'update'])->name('profile.update');
});

// Route ini hanya merespon 'ok' untuk memperbarui waktu sesi user
Route::post('/keep-alive', function () {
    return response()->json(['status' => 'Sesi diperpanjang']);
})->name('keep-alive');
