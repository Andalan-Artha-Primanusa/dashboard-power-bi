<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyContextController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Halaman pilih company
     * GET /company/select
     */
    public function select(Request $request)
    {
        $user = $request->user();

        // Kalau user punya relasi companies(), pakai itu.
        // Kalau enggak, fallback ke semua company aktif (khusus GM/SA)
        if ($user && method_exists($user, 'companies')) {
            $companies = $user->companies()
                ->where('is_active', 1)
                ->orderBy('name')
                ->get();
        } else {
            $companies = Company::query()
                ->where('is_active', 1)
                ->orderBy('name')
                ->get();
        }

        $activeCompanyId = session('company_id') ?: ($user->default_company_id ?? null);

        return view('company.select', compact('companies', 'activeCompanyId'));
    }

    /**
     * Switch company aktif → simpan ke session
     * POST /company/switch
     */
    public function switch(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'company_id' => ['required', 'uuid', 'exists:companies,id'],
        ]);

        // hanya company aktif yang bisa dipakai
        $company = Company::where('is_active', 1)->findOrFail($data['company_id']);

        // kalau ada pivot, pastikan user memang punya akses
        if ($user && method_exists($user, 'companies')) {
            $allowed = $user->companies()->where('companies.id', $company->id)->exists();
            abort_if(!$allowed, 403, 'Kamu tidak punya akses ke perusahaan ini.');
        }

        // Set company aktif ke session
        session(['company_id' => $company->id]);

        // optional safety: reset site biar gak nyangkut lintas company
        session()->forget('site_id');

        return back()->with('success', "Perusahaan aktif diganti ke: {$company->name} ✅");
    }

    /**
     * Set default company di profil user (persist)
     * POST /company/set-default
     */
    public function setDefault(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'company_id' => ['required', 'uuid', 'exists:companies,id'],
        ]);

        $company = Company::where('is_active', 1)->findOrFail($data['company_id']);

        if ($user && method_exists($user, 'companies')) {
            $allowed = $user->companies()->where('companies.id', $company->id)->exists();
            abort_if(!$allowed, 403, 'Kamu tidak punya akses ke perusahaan ini.');
        }

        $user->default_company_id = $company->id;
        $user->save();

        session(['company_id' => $company->id]);
        session()->forget('site_id');

        return back()->with('success', "Default perusahaan diset ke: {$company->name} ✅");
    }
}
