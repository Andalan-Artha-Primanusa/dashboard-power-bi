<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SiteAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:gm|super_admin']);
    }

    public function index(Request $request)
    {
        $only = $request->query('only'); // 'trashed' to show deleted
        $q    = trim((string) $request->query('q'));

        $query = Site::query()->latest();
        if ($only === 'trashed') $query->onlyTrashed();

        if ($q) {
            $query->where(function ($w) use ($q) {
                $w->where('name','like',"%$q%")
                  ->orWhere('code','like',"%$q%")
                  ->orWhere('region','like',"%$q%");
            });
        }

        $sites = $query->paginate(20)->withQueryString();

        return view('admin.sites.index', compact('sites','only','q'));
    }

    public function create()
    {
        return view('admin.sites.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'      => ['required','string','max:20','unique:sites,code'],
            'name'      => ['required','string','max:255'],
            'region'    => ['nullable','string','max:255'],
            'address'   => ['nullable','string','max:500'],
            'lat'       => ['nullable','numeric','between:-90,90'],
            'lng'       => ['nullable','numeric','between:-180,180'],
            'is_active' => ['sometimes','boolean'],
            'config'    => ['nullable','array'],
        ]);

        DB::transaction(function () use ($data) {
            Site::create([
                'code'       => $data['code'],
                'name'       => $data['name'],
                'region'     => $data['region']   ?? null,
                'address'    => $data['address']  ?? null,
                'lat'        => $data['lat']      ?? null,
                'lng'        => $data['lng']      ?? null,
                'is_active'  => $data['is_active'] ?? true,
                'config'     => $data['config']   ?? null,
                'created_by' => auth()->id(),
            ]);
        });

        return redirect()->route('admin.sites.index')->with('success','Site created.');
    }

    public function edit(Site $site)
    {
        return view('admin.sites.edit', compact('site'));
    }

    public function update(Request $request, Site $site)
    {
        $data = $request->validate([
            'code'      => ['required','string','max:20','unique:sites,code,'.$site->id.',id'],
            'name'      => ['required','string','max:255'],
            'region'    => ['nullable','string','max:255'],
            'address'   => ['nullable','string','max:500'],
            'lat'       => ['nullable','numeric','between:-90,90'],
            'lng'       => ['nullable','numeric','between:-180,180'],
            'is_active' => ['sometimes','boolean'],
            'config'    => ['nullable','array'],
        ]);

        DB::transaction(function () use ($data,$site) {
            $site->update([
                'code'      => $data['code'],
                'name'      => $data['name'],
                'region'    => $data['region']   ?? null,
                'address'   => $data['address']  ?? null,
                'lat'       => $data['lat']      ?? null,
                'lng'       => $data['lng']      ?? null,
                'is_active' => $data['is_active'] ?? true,
                'config'    => $data['config']   ?? null,
            ]);
        });

        return redirect()->route('admin.sites.index')->with('success','Site updated.');
    }

    public function destroy(Site $site)
    {
        $site->delete();
        return back()->with('success','Site deleted.');
    }

    public function restore(string $id)
    {
        $site = Site::onlyTrashed()->findOrFail($id);
        $site->restore();
        return back()->with('success','Site restored.');
    }

    public function forceDelete(string $id)
    {
        $site = Site::onlyTrashed()->findOrFail($id);
        $site->forceDelete();
        return back()->with('success','Site permanently deleted.');
    }

    public function toggleActive(Site $site)
    {
        $site->update(['is_active' => ! (bool) $site->is_active]);
        return back()->with('success','Site status toggled.');
    }
}
