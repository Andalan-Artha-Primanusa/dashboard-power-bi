<?php

use Illuminate\Support\Facades\Route;

// === Controllers (Breeze default) ===
use App\Http\Controllers\ProfileController;

// === Power BI Controllers ===
use App\Http\Controllers\PowerBiController;
use App\Http\Controllers\Admin\PowerBiAdminController;

// === Admin User & Audit Controllers ===
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Admin\AuditLogController;

/*
|--------------------------------------------------------------------------
| Public / Landing
|--------------------------------------------------------------------------
| (Bisa diganti ke redirect('/login') kalau mau langsung ke login)
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
| Protected area (auth)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Profile (Breeze)
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',[ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',[ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Power BI - User facing
    |--------------------------------------------------------------------------
    | - /dashboards            : daftar report yang user bisa akses
    | - /dashboards/{report}   : lihat 1 report (UUID)
    */
    Route::get('/dashboards',          [PowerBiController::class, 'index'])->name('powerbi.index');
    Route::get('/dashboards/{report}', [PowerBiController::class, 'show'])->name('powerbi.show');

    /*
    |--------------------------------------------------------------------------
    | Power BI - Admin CRUD (soft delete + restore)
    |--------------------------------------------------------------------------
    | Tanpa hardcode: batasi dengan role GM & Super Admin
    */
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

    /*
    |--------------------------------------------------------------------------
    | USER MANAGEMENT (GM & Super Admin)
    |--------------------------------------------------------------------------
    | Ganti division & reset password user.
    */
    Route::prefix('admin/users')
        ->name('admin.users.')
        ->middleware('role:gm,super_admin')
        ->group(function () {
            Route::get('/',                       [UserAdminController::class, 'index'])->name('index');
            Route::get('/{user}/edit',            [UserAdminController::class, 'edit'])->name('edit');
            Route::patch('/{user}/division',      [UserAdminController::class, 'updateDivision'])->name('updateDivision');
            Route::post('/{user}/reset-password', [UserAdminController::class, 'resetPassword'])->name('resetPassword');
        });

    /*
    |--------------------------------------------------------------------------
    | AUDIT LOG (Super Admin only)
    |--------------------------------------------------------------------------
    */
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

require __DIR__.'/auth.php';
