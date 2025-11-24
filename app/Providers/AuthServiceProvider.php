<?php

namespace App\Providers;

use App\Models\PowerBiReport;
use App\Models\User;
use App\Models\Company; // <-- tambah ini
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // === Super Admin override: semua ability auto-true ===
        Gate::before(function ($user, $ability) {
            return (($user->role ?? 'user') === 'super_admin') ? true : null;
        });

        // === Lihat report Power BI (per user/divisi) ===
        Gate::define('view-powerbi', function (User $user, PowerBiReport $report) {
            return PowerBiReport::visibleTo($user)->whereKey($report->id)->exists();
        });

        // === Kelola Power BI (GM & Super Admin) ===
        Gate::define('manage-powerbi', function (User $user) {
            return in_array($user->role ?? 'user', ['gm', 'super_admin'], true);
        });

        // === Kelola User (GM & Super Admin) ===
        Gate::define('manage-users', function (User $user) {
            return in_array($user->role ?? 'user', ['gm', 'super_admin'], true);
        });

        // === Lihat Audit (Super Admin only) ===
        Gate::define('view-audit', function (User $user) {
            return ($user->role ?? 'user') === 'super_admin';
        });

        /*
        |=====================================================
        | COMPANY GATES
        |=====================================================
        */

        // === Lihat/akses company tertentu ===
        Gate::define('view-company', function (User $user, Company $company) {

            // kalau pivot many-to-many
            if (method_exists($user, 'companies')) {
                return $user->companies()
                    ->whereKey($company->id)
                    ->exists();
            }

            // fallback kalau masih ada kolom single company_id
            if (!empty($user->company_id)) {
                return $user->company_id === $company->id;
            }

            // fallback lain: default_company_id
            if (!empty($user->default_company_id)) {
                return $user->default_company_id === $company->id;
            }

            return false;
        });

        // === Switch company (aturan sama dengan view-company) ===
        Gate::define('switch-company', function (User $user, Company $company) {
            return Gate::forUser($user)->check('view-company', $company);
        });

        // === Kelola company (GM & Super Admin) ===
        Gate::define('manage-companies', function (User $user) {
            return in_array($user->role ?? 'user', ['gm', 'super_admin'], true);
        });
    }
}
