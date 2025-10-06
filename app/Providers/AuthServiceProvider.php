<?php

namespace App\Providers;

use App\Models\PowerBiReport;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

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
    }
}
