<?php // app/Http/Controllers/PowerBiController.php

namespace App\Http\Controllers;

use App\Models\PowerBiReport;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class PowerBiController extends Controller
{
    /**
     * Backward-compat: jika ada yang masih akses /dashboards (index lama),
     * arahkan ke daftar Site.
     */
    public function index()
    {
        return redirect()->route('powerbi.sites');
    }

    /**
     * Helper: normalisasi role → apakah GM?
     */
    protected function isGM($user): bool
    {
        $raw = $user->role->key
            ?? $user->role->slug
            ?? $user->role->name
            ?? (is_string($user->role ?? null) ? $user->role : '')
            ?? '';

        $norm = Str::of($raw)->lower()->replace(['_', '-'], ' ')->squish()->toString();
        $map  = [
            'general manager' => 'gm',
            'generalmanager'  => 'gm',
            'mgr'             => 'manager',
            'gm'              => 'gm',
            'manager'         => 'manager',
            'super admin'     => 'super_admin',
            'superadmin'      => 'super_admin',
            'sa'              => 'super_admin',
            'root'            => 'super_admin',
        ];
        $roleKey = $map[$norm] ?? $norm;

        return $roleKey === 'gm';
    }

    /**
     * STEP 1 — Daftar Site (GM bisa pilih; non-GM auto ke site aktif/default)
     */
    public function sites(Request $request)
{
    $user = $request->user();
    try { $user?->loadMissing('role'); } catch (\Throwable $e) {}

    // Cek role GM
    $raw = $user->role->key
        ?? $user->role->slug
        ?? $user->role->name
        ?? (is_string($user->role ?? null) ? $user->role : '')
        ?? '';
    $norm = \Illuminate\Support\Str::of($raw)->lower()->replace(['_', '-'], ' ')->squish()->toString();
    $map  = ['general manager'=>'gm','generalmanager'=>'gm','mgr'=>'manager','gm'=>'gm','manager'=>'manager','super admin'=>'super_admin','superadmin'=>'super_admin','sa'=>'super_admin','root'=>'super_admin'];
    $roleKey = $map[$norm] ?? $norm;
    $isGM = $roleKey === 'gm';

    // Tentukan site aktif/default
    $activeSiteId = session('site_id') ?? ($user->default_site_id ?? null);
    $activeSite   = $activeSiteId ? Site::find($activeSiteId) : null;

    // GM → tampilkan semua site
    if ($isGM) {
        $sites = Site::query()
            ->when(method_exists(Site::class,'active'), fn($q)=>$q->active(), fn($q)=>$q->where('is_active', true))
            ->orderBy('code')
            ->get(['id','code','name']);
    } else {
        // Non-GM → hanya site aktif (supaya muncul card-nya)
        $sites = $activeSite ? collect([$activeSite]) : collect();
    }

    return view('powerbi.sites', [
        'sites'      => $sites,
        'activeSite' => $activeSite,
        'isGM'       => $isGM,
    ]);
}


    /**
     * STEP 2 — Setelah pilih Site, tampilkan card Power BI reports untuk Site tsb
     * Non-GM: dipaksa hanya boleh melihat site aktif/default miliknya
     */
    public function siteReports(Request $request, Site $site)
    {
        $user = $request->user();
        try { $user?->loadMissing('role'); } catch (\Throwable $e) {}

        $isGM = $this->isGM($user);

        // Tentukan site aktif/default user
        $userActiveSiteId = session('site_id') ?? ($user->default_site_id ?? null);

        // NON-GM guard: jika bukan GM dan mencoba akses site lain → redirect ke site aktifnya
        if (!$isGM) {
            if (!$userActiveSiteId) {
                // Tidak punya default site juga → tolak atau arahkan balik ke daftar (yang akan menampilkan info)
                return redirect()->route('powerbi.sites');
            }
            if ($site->id !== $userActiveSiteId) {
                $forcedSite = Site::find($userActiveSiteId);
                if ($forcedSite) {
                    return redirect()->route('powerbi.site.reports', $forcedSite);
                } else {
                    return redirect()->route('powerbi.sites');
                }
            }
        } else {
            // Optional: ketika GM memilih site, simpan ke session agar quick-open mengikuti
            session(['site_id' => $site->id]);
        }

        // Ambil report yang visible dan terasosiasi dengan site ini
        $reports = PowerBiReport::with(['sites:id,code,name'])
            ->visibleTo($user) // scope: cek grant user/division/site/global
            ->whereHas('sites', fn($q) => $q->whereKey($site->id)) // restrict: hanya report milik site ini
            ->orderBy('name')
            ->paginate(12);

        return view('powerbi.site_reports', compact('site','reports'));
    }

    /**
     * STEP 3 — Detail report: embed Power BI
     */
   public function show(Request $request, PowerBiReport $report)
{
    $user = $request->user();

    // cek apakah report ini memang visible untuk user tsb
    $allowed = PowerBiReport::query()
        ->visibleTo($user)
        ->whereKey($report->id)
        ->exists();

    abort_unless($allowed, 403);

    $embedUrl = $report->embedUrlWithUI();

    return view('powerbi.show', compact('report','embedUrl'));
}
}
