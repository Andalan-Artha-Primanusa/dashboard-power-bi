<?php

use Illuminate\Support\Facades\Route;

// ====== Breeze (default) ======
use App\Http\Controllers\ProfileController;

// ====== Power BI (User-facing) ======
use App\Http\Controllers\PowerBiController;
use App\Http\Controllers\DashboardController;

// ====== Admin Controllers ======
use App\Http\Controllers\Admin\PowerBiAdminController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\DivisionAdminController;
use App\Http\Controllers\Admin\SiteAdminController;
use App\Http\Controllers\Admin\SiteContextController;

// ====== Company Controllers ======
use App\Http\Controllers\Admin\CompanyAdminController;
use App\Http\Controllers\Admin\CompanyContextController;

/*
|--------------------------------------------------------------------------
| Route Patterns
|--------------------------------------------------------------------------
*/
Route::pattern('user',     '[0-9a-fA-F-]{36}');
Route::pattern('division', '[0-9a-fA-F-]{36}');
Route::pattern('site',     '[0-9a-fA-F-]{36}');
Route::pattern('company',  '[0-9a-fA-F-]{36}');
Route::pattern('report',   '[0-9a-fA-F-]{36}');

/*
|--------------------------------------------------------------------------
| Public / Landing
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Dashboard (Breeze)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Protected area (Auth)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    /*
    |=========================
    | Profile (Breeze)
    |=========================
    */
    Route::get('/profile',   [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',[ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |=========================
    | COMPANY CONTEXT
    |=========================
    */
    Route::prefix('company')->name('company.')->group(function () {
        Route::get('/select',       [CompanyContextController::class, 'select'])->name('select');
        Route::post('/switch',      [CompanyContextController::class, 'switch'])->name('switch');
        Route::post('/set-default', [CompanyContextController::class, 'setDefault'])->name('setDefault');
    });

    /*
    |=========================
    | SITE CONTEXT
    |=========================
    */
    Route::prefix('site')->name('site.')->group(function () {
        Route::get('/select',       [SiteContextController::class, 'select'])->name('select');
        Route::post('/switch',      [SiteContextController::class, 'switch'])->name('switch');
        Route::post('/set-default', [SiteContextController::class, 'setDefault'])->name('setDefault');
    });

    /*
    |=========================
    | POWER BI - USER
    |=========================
    */
    Route::prefix('dashboards')->name('powerbi.')->group(function () {
        Route::get('/', [PowerBiController::class, 'sites'])->name('index');
        Route::get('/sites', fn() => redirect()->route('powerbi.index'))->name('sites');

        Route::get('/site/{site}',     [PowerBiController::class, 'siteReports'])->name('site.reports');
        Route::get('/report/{report}', [PowerBiController::class, 'show'])->name('show');

        Route::get('/{report}', function ($report) {
            return redirect()->route('powerbi.show', ['report' => $report]);
        })->where('report', '[0-9a-fA-F-]{36}');
    });

    /*
    |=========================
    | POWER BI - ADMIN
    |=========================
    */
    Route::prefix('admin/powerbi')
        ->name('admin.powerbi.')
        ->middleware('role:gm|super_admin|creator')
        ->group(function () {
            Route::get('/',              [PowerBiAdminController::class, 'index'])->name('index');
            Route::get('/create',        [PowerBiAdminController::class, 'create'])->name('create');
            Route::post('/',             [PowerBiAdminController::class, 'store'])->name('store');
            Route::get('/{report}/edit', [PowerBiAdminController::class, 'edit'])->name('edit');
            Route::put('/{report}',      [PowerBiAdminController::class, 'update'])->name('update');
            Route::delete('/{report}',   [PowerBiAdminController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/restore', [PowerBiAdminController::class, 'restore'])->name('restore');
        });

    /*
    |=========================
    | COMPANIES - ADMIN
    |=========================
    */
    Route::prefix('admin/companies')
        ->name('admin.companies.')
        ->middleware('role:gm|super_admin|creator')
        ->group(function () {
            Route::get('/',               [CompanyAdminController::class, 'index'])->name('index');
            Route::get('/create',         [CompanyAdminController::class, 'create'])->name('create');
            Route::post('/',              [CompanyAdminController::class, 'store'])->name('store');
            Route::get('/{company}/edit', [CompanyAdminController::class, 'edit'])->name('edit');
            Route::put('/{company}',      [CompanyAdminController::class, 'update'])->name('update');
            Route::delete('/{company}',   [CompanyAdminController::class, 'destroy'])->name('destroy');

            Route::post('/{id}/restore',  [CompanyAdminController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force',  [CompanyAdminController::class, 'forceDelete'])->name('forceDelete');

            Route::patch('/{company}/toggle', [CompanyAdminController::class, 'toggleActive'])->name('toggle');
        });

    /*
    |=========================
    | SITES - ADMIN
    |=========================
    */
    Route::prefix('admin/sites')
        ->name('admin.sites.')
        ->middleware('role:gm|super_admin|creator')
        ->group(function () {
            Route::get('/',              [SiteAdminController::class, 'index'])->name('index');
            Route::get('/create',        [SiteAdminController::class, 'create'])->name('create');
            Route::post('/',             [SiteAdminController::class, 'store'])->name('store');
            Route::get('/{site}/edit',   [SiteAdminController::class, 'edit'])->name('edit');
            Route::put('/{site}',        [SiteAdminController::class, 'update'])->name('update');
            Route::delete('/{site}',     [SiteAdminController::class, 'destroy'])->name('destroy');

            Route::post('/{id}/restore', [SiteAdminController::class, 'restore'])->name('restore');
            Route::delete('/{id}/force', [SiteAdminController::class, 'forceDelete'])->name('forceDelete');

            Route::patch('/{site}/toggle', [SiteAdminController::class, 'toggleActive'])->name('toggle');
        });

    /*
    |=========================
    | DIVISION MANAGEMENT - ADMIN
    |=========================
    */
    Route::prefix('admin/divisions')
        ->name('admin.divisions.')
        ->middleware('role:gm|super_admin|creator')
        ->group(function () {
            Route::get('/',                  [DivisionAdminController::class, 'index'])->name('index');
            Route::get('/create',            [DivisionAdminController::class, 'create'])->name('create');
            Route::post('/',                 [DivisionAdminController::class, 'store'])->name('store');
            Route::get('/{division}/edit',   [DivisionAdminController::class, 'edit'])->name('edit');
            Route::put('/{division}',        [DivisionAdminController::class, 'update'])->name('update');
            Route::delete('/{division}',     [DivisionAdminController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/restore',     [DivisionAdminController::class, 'restore'])->name('restore');
        });

    /*
    |=========================
    | USERS - ADMIN
    |=========================
    */
    Route::prefix('admin/users')
        ->name('admin.users.')
        ->middleware('role:gm|super_admin|creator')
        ->group(function () {
            Route::get('/',                  [UserAdminController::class, 'index'])->name('index');
            Route::get('/create',            [UserAdminController::class, 'create'])->name('create');
            Route::post('/',                 [UserAdminController::class, 'store'])->name('store');

            Route::get('/{user}/edit',       [UserAdminController::class, 'edit'])->name('edit');

            // ✅ update utama user
            Route::put('/{user}',            [UserAdminController::class, 'update'])->name('update');
            Route::patch('/{user}',          [UserAdminController::class, 'update']); // alias PATCH

            // quick update fields
            Route::patch('/{user}/division', [UserAdminController::class, 'updateDivision'])->name('updateDivision');
            Route::patch('/{user}/site',     [UserAdminController::class, 'updateSite'])->name('updateSite');

            // reset password
            Route::post('/{user}/reset-password', [UserAdminController::class, 'resetPassword'])->name('resetPassword');

            // ✅ PHOTO routes (controller pakai updatePhoto/deletePhoto)
            Route::patch('/{user}/photo',    [UserAdminController::class, 'updatePhoto'])->name('updatePhoto');
            Route::post('/{user}/photo',     [UserAdminController::class, 'updatePhoto']); // alias POST biar form lama gak 405
            Route::delete('/{user}/photo',   [UserAdminController::class, 'deletePhoto'])->name('deletePhoto');

            // delete user
            Route::delete('/{user}',         [UserAdminController::class, 'destroy'])->name('destroy');
        });

    /*
    |=========================
    | AUDIT LOG (SUPER ADMIN + GM + CREATOR)
    |=========================
    */
    Route::prefix('admin/audit')
        ->name('admin.audit.')
        ->middleware(['auth', 'role:gm|super_admin|creator'])
        ->group(function () {
            Route::get('/',                        [AuditLogController::class, 'index'])->name('index');
            Route::get('/users/{user}',            [AuditLogController::class, 'showUser'])->name('user');
            Route::get('/export.csv',              [AuditLogController::class, 'exportCsv'])->name('export');
            Route::get('/users/{user}/export.csv', [AuditLogController::class, 'exportUserCsv'])->name('user.export');
        });
});

/*
|--------------------------------------------------------------------------
| Auth scaffolding (Breeze/Fortify/etc.)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
