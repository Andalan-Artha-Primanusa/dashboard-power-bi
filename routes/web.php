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

// ===== Sites (NEW) =====
use App\Http\Controllers\Admin\SiteAdminController;       // CRUD admin
use App\Http\Controllers\Admin\SiteContextController;     // pilih/switch site aktif user

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
    // SITE CONTEXT (NEW) - Semua user login boleh pilih site aktif
    // =========================
    Route::prefix('site')->name('site.')->group(function () {
        // Halaman pilih site (dropdown/grid)
        Route::get('/select', [SiteContextController::class, 'select'])->name('select');
        // Action set site aktif (simpan ke session / user default)
        Route::post('/switch', [SiteContextController::class, 'switch'])->name('switch');

        // Opsional: set sebagai default di profile user (kalau kamu sediakan)
        Route::post('/set-default', [SiteContextController::class, 'setDefault'])->name('setDefault');
    });

    // =========================
    // POWER BI - USER (VIEW ONLY)
    // =========================
    Route::prefix('dashboards')
        ->name('powerbi.')
        ->group(function () {
            Route::get('/',               [PowerBiController::class, 'index'])->name('index'); // list view-only
            Route::get('/{report}',       [PowerBiController::class, 'show'])->name('show');   // detail view-only
        });

    // =========================
    // POWER BI - ADMIN (GM & SUPER ADMIN)
    // =========================
    Route::prefix('admin/powerbi')
        ->name('admin.powerbi.')
        ->middleware('role:gm,super_admin')
        ->group(function () {
            Route::get('/',               [PowerBiAdminController::class, 'index'])->name('index');
            Route::get('/create',         [PowerBiAdminController::class, 'create'])->name('create');
            Route::post('/',              [PowerBiAdminController::class, 'store'])->name('store');
            Route::get('/{report}/edit',  [PowerBiAdminController::class, 'edit'])->name('edit');
            Route::put('/{report}',       [PowerBiAdminController::class, 'update'])->name('update');
            Route::delete('/{report}',    [PowerBiAdminController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/restore',  [PowerBiAdminController::class, 'restore'])->name('restore');
        });

    // =========================
    // SITES - ADMIN (NEW) (GM & SUPER ADMIN)
    // =========================
    Route::prefix('admin/sites')
        ->name('admin.sites.')
        ->middleware('role:gm,super_admin')
        ->group(function () {
            Route::get('/',               [SiteAdminController::class, 'index'])->name('index');
            Route::get('/create',         [SiteAdminController::class, 'create'])->name('create');
            Route::post('/',              [SiteAdminController::class, 'store'])->name('store');
            Route::get('/{site}/edit',    [SiteAdminController::class, 'edit'])->name('edit');
            Route::put('/{site}',         [SiteAdminController::class, 'update'])->name('update');
            Route::delete('/{site}',      [SiteAdminController::class, 'destroy'])->name('destroy');

            // Soft delete utilities
            Route::post('/{id}/restore',  [SiteAdminController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force',  [SiteAdminController::class, 'forceDelete'])->name('forceDelete');

            // Opsional: toggle aktif/nonaktif
            Route::patch('/{site}/toggle', [SiteAdminController::class, 'toggleActive'])->name('toggle');
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

    // =========================
    // USERS - ADMIN (GM & SUPER ADMIN)
    // =========================
    Route::prefix('admin/users')
        ->name('admin.users.')
        ->middleware('role:gm,super_admin')
        ->group(function () {
            Route::get('/create',  [UserAdminController::class, 'create'])->name('create');
            Route::post('/',       [UserAdminController::class, 'store'])->name('store');
            Route::get('/',        [UserAdminController::class, 'index'])->name('index');
            Route::get('/{user}/edit',           [UserAdminController::class, 'edit'])->name('edit');
            Route::patch('/{user}/division',     [UserAdminController::class, 'updateDivision'])->name('updateDivision');
            Route::post('/{user}/reset-password',[UserAdminController::class, 'resetPassword'])->name('resetPassword');
            Route::delete('/{user}',             [UserAdminController::class, 'destroy'])->name('destroy');
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
