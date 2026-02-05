<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Grup Route Dashboard (Gunakan Middleware)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard/manager', [DashboardController::class, 'manager'])->middleware('role:manager');
    Route::get('/dashboard/staff', [DashboardController::class, 'staff'])->middleware('role:staff');
});
