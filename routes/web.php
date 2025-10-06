<?php

use Illuminate\Support\Facades\Route;

// Breeze default
use App\Http\Controllers\ProfileController;

// Power BI (user-facing)
use App\Http\Controllers\PowerBiController;

// Power BI Admin
use App\Http\Controllers\Admin\PowerBiAdminController;

// Admin User & Audit
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Admin\AuditLogController;

// Division Admin
use App\Http\Controllers\Admin\DivisionAdminController;

/*
|--------------------------------------------------------------------------
| Public / Landing
|--------------------------------------------------------------------------
*/

Route::get('/', fn() => view('welcome'));

/*
|--------------------------------------------------------------------------
| Dashboard (Breeze)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', fn() => view('dashboard'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Protected area (Auth)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // =========================
    // Profile (Breeze)
    // =========================
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // =========================
    // POWER BI - USER (VIEW ONLY)
    // =========================
    // Semua user yang login bisa mengakses halaman ini.
    // List hanya menampilkan report yang diberi akses (via scopeVisibleTo).
    // Show dilindungi Gate 'view-powerbi'.
    Route::prefix('dashboards')
        ->name('powerbi.')
        ->group(function () {
            Route::get('/',               [PowerBiController::class, 'index'])->name('index');        // list view-only
            Route::get('/{report}',       [PowerBiController::class, 'show'])->name('show');          // detail view-only
        });

    // =========================
    // POWER BI - ADMIN (GM & SUPER ADMIN)
    // =========================
    // routes/web.php
    Route::prefix('admin/powerbi')
        ->name('admin.powerbi.')
        ->middleware('role:gm,super_admin')   // <= ganti ini
        ->group(function () {
            Route::get('/',               [\App\Http\Controllers\Admin\PowerBiAdminController::class, 'index'])->name('index');
            Route::get('/create',         [\App\Http\Controllers\Admin\PowerBiAdminController::class, 'create'])->name('create');
            Route::post('/',              [\App\Http\Controllers\Admin\PowerBiAdminController::class, 'store'])->name('store');
            Route::get('/{report}/edit',  [\App\Http\Controllers\Admin\PowerBiAdminController::class, 'edit'])->name('edit');
            Route::put('/{report}',       [\App\Http\Controllers\Admin\PowerBiAdminController::class, 'update'])->name('update');
            Route::delete('/{report}',    [\App\Http\Controllers\Admin\PowerBiAdminController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/restore',  [\App\Http\Controllers\Admin\PowerBiAdminController::class, 'restore'])->name('restore');
        });


    // =========================
    // DIVISION MANAGEMENT (GM & SUPER ADMIN)
    // =========================
    Route::prefix('admin/divisions')
        ->name('admin.divisions.')
        ->middleware('role:gm,super_admin')
        ->group(function () {
            Route::get('/',                 [DivisionAdminController::class, 'index'])->name('index');
            Route::get('/create',           [DivisionAdminController::class, 'create'])->name('create');
            Route::post('/',                [DivisionAdminController::class, 'store'])->name('store');
            Route::get('/{division}/edit',  [DivisionAdminController::class, 'edit'])->name('edit');
            Route::put('/{division}',       [DivisionAdminController::class, 'update'])->name('update');
            Route::delete('/{division}',    [DivisionAdminController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/restore',    [DivisionAdminController::class, 'restore'])->name('restore');
        });

    Route::prefix('admin/users')
    ->name('admin.users.')
    ->middleware('role:gm,super_admin')
    ->group(function () {
        // optional: form tambah user
        Route::get('/create',  [\App\Http\Controllers\Admin\UserAdminController::class, 'create'])->name('create');
        Route::post('/',       [\App\Http\Controllers\Admin\UserAdminController::class, 'store'])->name('store');
        Route::get('/',                      [\App\Http\Controllers\Admin\UserAdminController::class, 'index'])->name('index');
        Route::get('/{user}/edit',           [\App\Http\Controllers\Admin\UserAdminController::class, 'edit'])->name('edit');
        Route::patch('/{user}/division',     [\App\Http\Controllers\Admin\UserAdminController::class, 'updateDivision'])->name('updateDivision');
        // actions
        Route::post('/{user}/reset-password',[\App\Http\Controllers\Admin\UserAdminController::class, 'resetPassword'])->name('resetPassword');
        Route::delete('/{user}',             [\App\Http\Controllers\Admin\UserAdminController::class, 'destroy'])->name('destroy');
    });


    // =========================
    // AUDIT LOG (SUPER ADMIN ONLY)
    // =========================
    Route::prefix('admin/audit')
        ->name('admin.audit.')
        ->middleware('role:super_admin')
        ->group(function () {
            Route::get('/',                    [AuditLogController::class, 'index'])->name('index');
            Route::get('/users/{user}',        [AuditLogController::class, 'showUser'])->name('showUser');
            Route::get('/export/csv',          [AuditLogController::class, 'exportCsv'])->name('exportCsv');
            Route::get('/users/{user}/export', [AuditLogController::class, 'exportUserCsv'])->name('exportUserCsv');
        });
});

require __DIR__ . '/auth.php';
