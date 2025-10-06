<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\PowerBiReport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        /**
         * Super Admin override:
         * Jika user.role === 'super_admin', maka semua ability akan auto-true.
         */
        Gate::before(function ($user, $ability) {
            return ($user->role ?? 'user') === 'super_admin' ? true : null;
        });

        /**
         * Gate: view-powerbi
         * User bisa lihat report kalau dia punya akses langsung (pivot user-report)
         * atau lewat division.
         */
        Gate::define('view-powerbi', function (User $user, PowerBiReport $report) {
            return PowerBiReport::visibleTo($user)->whereKey($report->id)->exists();
        });

        /**
         * Gate: manage-powerbi
         * GM & Super Admin boleh CRUD Power BI report.
         */
        Gate::define('manage-powerbi', function (User $user) {
            return in_array($user->role, ['gm', 'super_admin']);
        });

        /**
         * Gate: manage-users
         * GM & Super Admin boleh kelola user (ganti division, reset password).
         */
        Gate::define('manage-users', function (User $user) {
            return in_array($user->role, ['gm', 'super_admin']);
        });

        /**
         * Gate: view-audit
         * Hanya Super Admin (atau override) yang boleh lihat audit log.
         */
        Gate::define('view-audit', function (User $user) {
            return $user->role === 'super_admin';
        });
    }
}
