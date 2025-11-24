<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanyAdminController extends Controller
{
    public function __construct()
    {
        // role middleware sudah dipasang di routes
        $this->middleware(['auth']);
    }

    /**
     * LIST companies
     * /admin/companies
     */
    public function index(Request $request)
    {
        $q      = trim($request->q ?? '');
        $active = $request->active; // 1/0/null
        $trash  = $request->trash;  // 1 untuk lihat soft deleted

        $query = Company::query()->orderByDesc('updated_at');

        if ($trash) {
            $query->onlyTrashed();
        }

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('code', 'like', "%{$q}%");
            });
        }

        if ($active !== null && $active !== '') {
            $query->where('is_active', (int)$active);
        }

        $companies = $query->paginate(15)->withQueryString();

        return view('admin.companies.index', compact('companies', 'q', 'active', 'trash'));
    }

    /**
     * FORM create
     * /admin/companies/create
     */
    public function create()
    {
        return view('admin.companies.create');
    }

    /**
     * STORE company baru
     * POST /admin/companies
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'code'        => ['required', 'string', 'max:50', 'unique:companies,code'],
            'name'        => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active'   => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = (bool)($data['is_active'] ?? true);

        Company::create($data);

        return redirect()
            ->route('admin.companies.index')
            ->with('success', 'Perusahaan berhasil ditambahkan ✅');
    }

    /**
     * FORM edit
     * /admin/companies/{company}/edit
     */
    public function edit(Company $company)
    {
        return view('admin.companies.edit', compact('company'));
    }

    /**
     * UPDATE company
     * PUT /admin/companies/{company}
     */
    public function update(Request $request, Company $company)
    {
        $data = $request->validate([
            'code'        => [
                'required', 'string', 'max:50',
                Rule::unique('companies', 'code')->ignore($company->id),
            ],
            'name'        => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active'   => ['nullable', 'boolean'],
        ]);

        $company->update([
            'code'        => $data['code'],
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active'   => (bool)($data['is_active'] ?? false),
        ]);

        return redirect()
            ->route('admin.companies.index')
            ->with('success', 'Perusahaan berhasil diupdate ✅');
    }

    /**
     * SOFT DELETE
     * DELETE /admin/companies/{company}
     */
    public function destroy(Company $company)
    {
        $company->delete();

        return back()->with('success', 'Perusahaan dihapus (soft delete) ✅');
    }

    /**
     * RESTORE soft deleted
     * POST /admin/companies/{id}/restore
     */
    public function restore(string $id)
    {
        $company = Company::onlyTrashed()->findOrFail($id);
        $company->restore();

        return back()->with('success', 'Perusahaan berhasil direstore ✅');
    }

    /**
     * FORCE DELETE
     * DELETE /admin/companies/{id}/force
     */
    public function forceDelete(string $id)
    {
        $company = Company::onlyTrashed()->findOrFail($id);
        $company->forceDelete();

        return back()->with('success', 'Perusahaan dihapus permanen ✅');
    }

    /**
     * TOGGLE active/nonactive
     * PATCH /admin/companies/{company}/toggle
     */
    public function toggleActive(Company $company)
    {
        $company->is_active = !$company->is_active;
        $company->save();

        return back()->with('success', 'Status perusahaan diubah ✅');
    }
}
