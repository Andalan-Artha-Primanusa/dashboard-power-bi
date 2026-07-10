<?php

namespace App\Http\Controllers;

use App\Models\PowerBiReport;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PowerBiController extends Controller
{
    public function index()
    {
        return redirect()->route('powerbi.sites');
    }

    protected function roleKey($user): string
    {
        $role = $user->role ?? null;
        $raw = is_string($role)
            ? $role
            : ($role->key ?? $role->slug ?? $role->name ?? '');

        $norm = Str::of($raw)->lower()->replace(['_', '-'], ' ')->squish()->toString();
        $map = [
            'general manager' => 'gm',
            'generalmanager' => 'gm',
            'mgr' => 'manager',
            'gm' => 'gm',
            'manager' => 'manager',
            'super admin' => 'super_admin',
            'superadmin' => 'super_admin',
            'sa' => 'super_admin',
            'root' => 'super_admin',
            'creator' => 'creator',
        ];

        return $map[$norm] ?? $norm;
    }

    protected function canChooseContext($user): bool
    {
        return in_array($this->roleKey($user), ['gm', 'super_admin', 'creator'], true);
    }

    public function sites(Request $request)
    {
        $user = $request->user();
        try { $user?->loadMissing('role'); } catch (\Throwable $e) {}

        $canChooseContext = $this->canChooseContext($user);
        $activeCompanyId = session('company_id') ?? ($user->default_company_id ?? null);
        $activeSiteId = session('site_id') ?? ($user->default_site_id ?? null);
        $activeSite = $activeSiteId ? Site::find($activeSiteId) : null;

        if ($canChooseContext) {
            $sites = Site::query()
                ->when(method_exists(Site::class, 'active'), fn($q) => $q->active(), fn($q) => $q->where('is_active', true))
                ->when($activeCompanyId, fn($q) => $q->where('company_id', $activeCompanyId))
                ->orderBy('code')
                ->get(['id', 'company_id', 'code', 'name', 'region']);
        } else {
            $sites = $user->accessibleSites()
                ->when($activeCompanyId, fn($col) => $col->where('company_id', $activeCompanyId))
                ->values();

            if (!$activeSite && $sites->count() === 1) {
                $activeSite = $sites->first();
                session(['site_id' => $activeSite->id]);
            }
        }

        return view('powerbi.sites', [
            'sites' => $sites,
            'activeSite' => $activeSite,
            'isGM' => $canChooseContext,
        ]);
    }

    public function siteReports(Request $request, Site $site)
    {
        $user = $request->user();
        try { $user?->loadMissing('role'); } catch (\Throwable $e) {}

        if (!$this->canChooseContext($user)) {
            $allowed = method_exists($user, 'canAccessSite')
                ? $user->canAccessSite($site)
                : $site->id === ($user->default_site_id ?? null);

            if (!$allowed) {
                $fallbackSiteId = session('site_id') ?? ($user->default_site_id ?? null);
                $fallbackSite = $fallbackSiteId ? Site::find($fallbackSiteId) : null;

                return $fallbackSite
                    ? redirect()->route('powerbi.site.reports', $fallbackSite)
                    : redirect()->route('powerbi.sites');
            }
        }

        session(['site_id' => $site->id]);

        $reports = PowerBiReport::with(['sites:id,code,name'])
            ->visibleTo($user)
            ->whereHas('sites', fn($q) => $q->whereKey($site->id))
            ->orderBy('name')
            ->paginate(12);

        return view('powerbi.site_reports', compact('site', 'reports'));
    }

    public function show(Request $request, PowerBiReport $report)
    {
        $user = $request->user();

        $allowed = PowerBiReport::query()
            ->visibleTo($user)
            ->whereKey($report->id)
            ->exists();

        abort_unless($allowed, 403);

        $embedUrl = $report->embedUrlWithUI();

        return view('powerbi.show', compact('report', 'embedUrl'));
    }
}
