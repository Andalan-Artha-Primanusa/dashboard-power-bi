<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Site;
use App\Models\User;
use App\Models\Division;
use App\Models\PowerBiReport;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // === KPI counts ===
        $totalSites   = Site::when(method_exists(Site::class,'scopeActive'), fn($q)=>$q->active(), fn($q)=>$q->where('is_active',true))->count();
        $totalUsers   = User::count();
        $totalDivs    = Division::count();
        $totalReports = PowerBiReport::count(); // table: powerbi_reports

        // === Reports per Site ===
        // Utama: pakai pivot 'powerbi_report_site' sesuai model.
        // Fallback: kalau pivot ini tidak ada, coba 'power_bi_report_site'.
        // Last resort: join langsung ke kolom powerbi_reports.site_id bila memang 1..N.
        if (Schema::hasTable('powerbi_report_site')) {
            $reportsPerSite = Site::query()
                ->select([
                    'sites.id',
                    'sites.code',
                    'sites.name',
                    DB::raw('COUNT(pivot.report_id) AS reports_count'),
                ])
                ->leftJoin('powerbi_report_site AS pivot', 'pivot.site_id', '=', 'sites.id')
                ->when(method_exists(Site::class,'scopeActive'), fn($q)=>$q->active(), fn($q)=>$q->where('sites.is_active',true))
                ->groupBy('sites.id','sites.code','sites.name')
                ->orderBy('sites.code')
                ->get();
        } elseif (Schema::hasTable('power_bi_report_site')) {
            // Nama lama/alternatif
            $reportsPerSite = Site::query()
                ->select([
                    'sites.id',
                    'sites.code',
                    'sites.name',
                    DB::raw('COUNT(pivot.report_id) AS reports_count'),
                ])
                ->leftJoin('power_bi_report_site AS pivot', 'pivot.site_id', '=', 'sites.id')
                ->when(method_exists(Site::class,'scopeActive'), fn($q)=>$q->active(), fn($q)=>$q->where('sites.is_active',true))
                ->groupBy('sites.id','sites.code','sites.name')
                ->orderBy('sites.code')
                ->get();
        } else {
            // Tidak ada pivot → asumsikan kolom powerbi_reports.site_id tersedia
            $reportsPerSite = Site::query()
                ->select([
                    'sites.id',
                    'sites.code',
                    'sites.name',
                    DB::raw('COUNT(r.id) AS reports_count'),
                ])
                ->leftJoin('powerbi_reports AS r', 'r.site_id', '=', 'sites.id')
                ->when(method_exists(Site::class,'scopeActive'), fn($q)=>$q->active(), fn($q)=>$q->where('sites.is_active',true))
                ->groupBy('sites.id','sites.code','sites.name')
                ->orderBy('sites.code')
                ->get();
        }

        // === Users per Division ===
        $usersPerDivision = Division::query()
            ->select('divisions.id','divisions.name', DB::raw('COUNT(users.id) AS users_count'))
            ->leftJoin('users', 'users.division_id', '=', 'divisions.id')
            ->groupBy('divisions.id','divisions.name')
            ->orderBy('divisions.name')
            ->get();

        // === Latest Power BI Reports === (table: powerbi_reports)
        $latestReports = PowerBiReport::query()
            ->with(['sites:id,code']) // pakai relasi belongsToMany ke pivot 'powerbi_report_site'
            ->latest()
            ->take(8)
            ->get(['id','name','created_at']);

        return view('dashboard', [
            'totalSites'       => $totalSites,
            'totalUsers'       => $totalUsers,
            'totalDivs'        => $totalDivs,
            'totalReports'     => $totalReports,
            'reportsPerSite'   => $reportsPerSite,
            'usersPerDivision' => $usersPerDivision,
            'latestReports'    => $latestReports,
        ]);
    }
}
