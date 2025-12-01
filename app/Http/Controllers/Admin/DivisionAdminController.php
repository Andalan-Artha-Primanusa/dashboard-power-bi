<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DivisionAdminController extends Controller
{
    public function __construct()
    {
        // Batasi ke GM & Super Admin sesuai alias middleware yang sudah kamu buat
        $this->middleware(['auth', 'role:gm,super_admin,creator']);
    }

    /**
     * List divisions (termasuk filter search & opsi tampilkan yang terhapus)
     */
    public function index(Request $request)
    {
        $q       = trim((string) $request->get('q'));
        $showDel = (bool) $request->boolean('with_trashed', false);

        $divisions = Division::query()
            ->when($showDel, fn($x) => $x->withTrashed())
            ->when($q, function ($x) use ($q) {
                $x->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                      ->orWhere('code', 'like', "%{$q}%");
                });
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.divisions.index', compact('divisions', 'q', 'showDel'));
    }

    /**
     * Form create
     */
    public function create()
    {
        $division = new Division(); // model kosong buat form
        return view('admin.divisions.form', compact('division'));
    }

    /**
     * Simpan record baru
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100|unique:divisions,name',
            'code'        => 'required|string|max:20|unique:divisions,code',
            'description' => 'nullable|string|max:500',
            'is_active'   => 'sometimes|boolean',
        ]);

        $division = Division::create([
            'id'          => (string) Str::uuid(),
            'name'        => $data['name'],
            'code'        => $data['code'],
            'description' => $data['description'] ?? null,
            'is_active'   => $data['is_active'] ?? true,
        ]);

        // catat audit (jika helper audit() sudah ditambahkan)
        if (function_exists('audit')) {
            audit('division.create', ['division' => $division->toArray()], Division::class, $division->id);
        }

        return redirect()
            ->route('admin.divisions.index')
            ->with('status', 'Division created.');
    }

    /**
     * Form edit
     */
    public function edit(Division $division)
    {
        return view('admin.divisions.form', compact('division'));
    }

    /**
     * Update record
     */
    public function update(Request $request, Division $division)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100|unique:divisions,name,' . $division->id . ',id',
            'code'        => 'required|string|max:20|unique:divisions,code,' . $division->id . ',id',
            'description' => 'nullable|string|max:500',
            'is_active'   => 'sometimes|boolean',
        ]);

        $before = $division->getOriginal();

        $division->update([
            'name'        => $data['name'],
            'code'        => $data['code'],
            'description' => $data['description'] ?? null,
            'is_active'   => $data['is_active'] ?? $division->is_active,
        ]);

        if (function_exists('audit')) {
            audit('division.update', [
                'before' => $before,
                'after'  => $division->getAttributes(),
            ], Division::class, $division->id);
        }

        return redirect()
            ->route('admin.divisions.index')
            ->with('status', 'Division updated.');
    }

    /**
     * Soft delete
     */
    public function destroy(Division $division)
    {
        $division->delete();

        if (function_exists('audit')) {
            audit('division.delete', ['division_id' => $division->id], Division::class, $division->id);
        }

        return redirect()
            ->route('admin.divisions.index', ['with_trashed' => 1])
            ->with('status', 'Division deleted.');
    }

    /**
     * Restore soft-deleted
     */
    public function restore(string $id)
    {
        $division = Division::withTrashed()->findOrFail($id);
        $division->restore();

        if (function_exists('audit')) {
            audit('division.restore', ['division_id' => $division->id], Division::class, $division->id);
        }

        return redirect()
            ->route('admin.divisions.index', ['with_trashed' => 1])
            ->with('status', 'Division restored.');
    }
}
