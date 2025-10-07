<?php // app/Http/Controllers/PowerBiController.php

namespace App\Http\Controllers;

use App\Models\PowerBiReport;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

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
     * STEP 1 — Tampilkan daftar Site yang bisa dipilih user
     */
    public function sites(Request $request)
    {
        // Kalau perlu, batasi hanya site aktif/allowed. Untuk sekarang list semua.
        $sites = Site::query()
            ->orderBy('code')
            ->get(['id','code','name']);

        return view('powerbi.sites', compact('sites'));
    }

    /**
     * STEP 2 — Setelah user pilih Site, tampilkan card Power BI reports untuk Site tsb
     */
    public function siteReports(Request $request, Site $site)
    {
        $user = $request->user();

        // Ambil hanya report yang:
        // - visibleTo($user) (grant via user/division/site/global)
        // - SECARA KHUSUS ada relasi ke Site yang dipilih (restrict ke site ini)
        $reports = PowerBiReport::with(['sites:id,code,name'])
            ->visibleTo($user) // scope di model sudah cek user/division/site/global
            ->whereHas('sites', fn($q) => $q->whereKey($site->id)) // restrict: hanya report milik site ini
            ->latest()
            ->paginate(12);

        return view('powerbi.site_reports', compact('site','reports'));
    }

    /**
     * STEP 3 — Detail report: embed Power BI
     */
    public function show(PowerBiReport $report)
    {
        // Pastikan user berhak melihat (Gate kamu sudah mengandalkan scope visibleTo)
        abort_unless(Gate::allows('view-powerbi', $report), 403);

        $embedUrl = $report->embedUrlWithUI();
        return view('powerbi.show', compact('report','embedUrl'));
    }
}
