<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Site;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SiteAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:gm|super_admin']);
    }

    /** Redirect helper: kembali ke index, bawa filter (only, q, company_id) jika ada */
    private function toIndex(Request $request, array $extra = [])
    {
        $params = array_filter(
            array_merge($request->only(['only','q','company_id']), $extra),
            fn($v) => $v !== null && $v !== ''
        );

        return redirect()->route('admin.sites.index', $params);
    }

    /** include trashed saat cari by key */
    private function findSite($key, bool $withTrashed = true): Site
    {
        $q = Site::query();
        if ($withTrashed) $q->withTrashed();
        return $q->whereKey($key)->firstOrFail();
    }

    public function index(Request $request)
    {
        $only = $request->query('only');
        $q    = trim((string) $request->query('q'));

        /**
         * ✅ ADMIN DEFAULT = tampil semua sites
         * Filter company hanya kalau query company_id diisi.
         */
        $companyId = $request->filled('company_id')
            ? $request->query('company_id')
            : null;

        $sites = Site::query()
            ->with('company') // biar badge company di view jalan
            ->when($companyId, fn($qb) => $qb->where('company_id', $companyId))
            ->when($only === 'trashed', fn($qb) => $qb->onlyTrashed())
            ->when($q, fn($w) => $w->where(fn($x) =>
                $x->where('name','like',"%{$q}%")
                  ->orWhere('code','like',"%{$q}%")
                  ->orWhere('region','like',"%{$q}%")
            ))
            ->orderByDesc('updated_at')
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        // dropdown filter company di UI index
        $companies = Company::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()
            ->view('admin.sites.index', compact('sites','only','q','companies','companyId'))
            ->header('Cache-Control','no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma','no-cache');
    }

    public function create()
    {
        $companies = Company::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // boleh default ke session company aktif biar enak
        $companyId = session('company_id');

        return view('admin.sites.create', compact('companies','companyId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id' => ['required','uuid','exists:companies,id'],

            // ✅ unique code per company
            'code' => [
                'required','string','max:20',
                Rule::unique('sites', 'code')->where(fn($q) =>
                    $q->where('company_id', $request->company_id)
                )
            ],

            'name'   => ['required','string','max:255'],
            'region' => ['nullable','string','max:255'],
            'address'=> ['nullable','string','max:500'],
            'lat'    => ['nullable','numeric','between:-90,90'],
            'lng'    => ['nullable','numeric','between:-180,180'],
            'is_active'=>['sometimes','boolean'],
            'config' => ['nullable','array'],
        ]);

        DB::transaction(fn() => Site::create([
            'company_id' => $data['company_id'],
            'code'       => $data['code'],
            'name'       => $data['name'],
            'region'     => $data['region'] ?? null,
            'address'    => $data['address'] ?? null,
            'lat'        => $data['lat'] ?? null,
            'lng'        => $data['lng'] ?? null,
            'is_active'  => $data['is_active'] ?? true,
            'config'     => $data['config'] ?? null,
            'created_by' => auth()->id(),
        ]));

        return $this->toIndex($request, ['company_id'=>$data['company_id']])
            ->with('success','Site created.');
    }

    public function edit($site)
    {
        $site = $this->findSite($site, true);

        $companies = Company::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.sites.edit', compact('site','companies'));
    }

    public function update(Request $request, $site)
    {
        $site = $this->findSite($site, true);

        $data = $request->validate([
            'company_id' => ['required','uuid','exists:companies,id'],

            // ✅ unique code per company (ignore current site)
            'code' => [
                'required','string','max:20',
                Rule::unique('sites', 'code')
                    ->ignore($site->id)
                    ->where(fn($q) =>
                        $q->where('company_id', $request->company_id)
                    )
            ],

            'name'   => ['required','string','max:255'],
            'region' => ['nullable','string','max:255'],
            'address'=> ['nullable','string','max:500'],
            'lat'    => ['nullable','numeric','between:-90,90'],
            'lng'    => ['nullable','numeric','between:-180,180'],
            'is_active'=>['sometimes','boolean'],
            'config' => ['nullable','array'],
        ]);

        DB::transaction(fn() => $site->update([
            'company_id' => $data['company_id'],
            'code'       => $data['code'],
            'name'       => $data['name'],
            'region'     => $data['region'] ?? null,
            'address'    => $data['address'] ?? null,
            'lat'        => $data['lat'] ?? null,
            'lng'        => $data['lng'] ?? null,
            'is_active'  => $data['is_active'] ?? true,
            'config'     => $data['config'] ?? null,
        ]));

        return $this->toIndex($request, ['company_id'=>$data['company_id']])
            ->with('success','Site updated.');
    }

    public function destroy(Request $request, $site)
    {
        $site = $this->findSite($site, true);
        $site->delete();

        return $this->toIndex($request)->with('success','Site deleted.');
    }

    public function restore(Request $request, string $id)
    {
        $site = Site::onlyTrashed()->findOrFail($id);
        $site->restore();

        return $this->toIndex($request, ['only'=>'trashed'])->with('success','Site restored.');
    }

    public function forceDelete(Request $request, string $id)
    {
        $site = Site::onlyTrashed()->findOrFail($id);
        $site->forceDelete();

        return $this->toIndex($request, ['only'=>'trashed'])->with('success','Site permanently deleted.');
    }

    public function toggleActive(Request $request, $site)
    {
        $site = $this->findSite($site, true);
        $site->update(['is_active' => ! (bool) $site->is_active]);

        return $this->toIndex($request)->with('success','Site status toggled.');
    }
}
