<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class SiteContextController extends Controller
{
    /**
     * Halaman pemilihan site (grid/list).
     * Show only active sites by default.
     */
    public function select(Request $request)
    {
        $sites = Site::query()
            ->when(!$request->boolean('include_inactive'), fn($q) => $q->where('is_active', true))
            ->orderBy('name')
            ->get(['id','code','name','region','is_active']);

        $currentSiteId = session('site_id');

        return view('admin.sites.select', compact('sites', 'currentSiteId'));
    }

    /**
     * Switch site aktif: simpan ke session('site_id').
     * Request: site_id (uuid).
     */
    public function switch(Request $request)
    {
        $data = $request->validate([
            'site_id' => ['required','uuid','exists:sites,id'],
        ]);

        // Optional: hanya izinkan switch ke site aktif
        $site = Site::where('id', $data['site_id'])->where('is_active', true)->firstOrFail();

        // Simpan ke session sebagai site aktif
        session(['site_id' => $site->id]);

        // Optional: kalau app kamu pakai locale/branding per-site, bisa ikut diset di session di sini
        // session(['site_config' => $site->config]);

        return back()->with('success', "Site switched to {$site->code} — {$site->name}.");
    }

    /**
     * Set site sebagai default user (jika kolom users.default_site_id ada).
     * Request: site_id (uuid).
     */
    public function setDefault(Request $request)
    {
        $data = $request->validate([
            'site_id' => ['required','uuid','exists:sites,id'],
        ]);

        // Validasi aktif
        $site = Site::where('id', $data['site_id'])->where('is_active', true)->firstOrFail();

        $user = $request->user();

        // Hanya update jika kolomnya tersedia agar aman di berbagai schema
        if (Schema::hasColumn('users', 'default_site_id')) {
            $user->forceFill(['default_site_id' => $site->id])->save();
        }

        // Update juga session aktif biar konsisten
        session(['site_id' => $site->id]);

        return back()->with('success', "Default site set to {$site->code} — {$site->name}.");
    }
}
