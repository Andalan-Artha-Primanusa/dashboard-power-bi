<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Division;
use App\Models\Site;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:gm|super_admin|creator']);
    }

    /**
     * LIST USERS (NO AUTO-FILTER SESSION)
     */
    public function index(Request $request)
    {
        $q        = trim((string) $request->query('q', ''));
        $division = $request->query('division_id');
        $roleKey  = $request->query('role');
        $siteId   = $request->query('site_id');
        $companyId= $request->query('company_id'); // ✅ hanya dari query

        // ✅ eager-load RELATION cuma kalau method-nya ada (biar gak 500)
        $with = [];
        if (method_exists(User::class, 'division')) {
            $with['division'] = fn($qb) => $qb->select('id','name');
        }
        if (method_exists(User::class, 'defaultSite')) {
            $with['defaultSite'] = fn($qb) => $qb->select('id','code','name','region');
        }
        if (method_exists(User::class, 'defaultCompany')) {
            $with['defaultCompany'] = fn($qb) => $qb->select('id','code','name');
        }
        if (method_exists(User::class, 'sites')) {
            $with['sites'] = fn($qb) => $qb->select('sites.id','code','name');
        }

        $users = User::query()
            ->with($with)

            // filter company cuma kalau kolom & query ada
            ->when(
                $companyId && Schema::hasColumn('users', 'default_company_id'),
                fn($qb) => $qb->where('default_company_id', $companyId)
            )

            ->when($q, function ($qb) use ($q) {
                $qb->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->when($division, fn($qb) => $qb->where('division_id', $division))
            ->when($roleKey, fn($qb) => $qb->where('role', $roleKey))
            ->when($siteId, fn($qb) => $qb->where('default_site_id', $siteId))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $companies = Company::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id','code','name']);

        $divisions = Division::query()
            ->orderBy('name')
            ->get(['id','name']);

        $sites = Site::query()
            ->orderBy('code')
            ->get(['id','code','name','region']);

        $roles = [
            'super_admin' => 'Super Admin',
            'gm'          => 'GM',
            'manager'     => 'Manager',
            'staff'       => 'Staff',
        ];

        return view('admin.users.index', compact(
            'users','companies','divisions','sites','roles',
            'q','division','roleKey','siteId','companyId'
        ));
    }

    /**
     * FORM CREATE USER
     */
    public function create(Request $request)
    {
        $companyId = $request->query('company_id') ?: session('company_id');

        $companies = Company::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id','code','name']);

        $divisions = Division::query()
            ->when(
                $companyId && Schema::hasColumn('divisions', 'company_id'),
                function ($qb) use ($companyId) {
                    $qb->where(function ($w) use ($companyId) {
                        $w->where('company_id', $companyId)
                          ->orWhereNull('company_id');
                    });
                }
            )
            ->orderBy('name')
            ->get(['id','name']);

        $sites = Site::query()
            ->when(
                $companyId && Schema::hasColumn('sites', 'company_id'),
                fn($qb) => $qb->where('company_id', $companyId)
            )
            ->orderBy('code')
            ->get(['id','code','name','region']);

        return view('admin.users.create', compact('companies','divisions','sites','companyId'));
    }

    /**
     * STORE USER BARU + MULTI-SITE
     */
    public function store(Request $r): RedirectResponse
    {
        $data = $r->validate([
            'name'               => ['required','string','max:150'],
            'email'              => ['required','email','max:190','unique:users,email'],

            'default_company_id' => ['nullable','uuid','exists:companies,id'],
            'division_id'        => ['nullable','uuid','exists:divisions,id'],
            'role'               => ['required','in:super_admin,gm,manager,staff,creator'],

            'default_site_id'    => ['nullable','uuid','exists:sites,id'],

            'site_ids'           => ['nullable','array'],
            'site_ids.*'         => ['uuid','exists:sites,id'],

            'password'           => ['nullable','string','min:8'],
            'photo'              => ['nullable','image','mimes:jpg,jpeg,png','max:2048'],
        ]);

        $plain = $data['password'] ?? Str::password(12);

        $photoPath = null;
        if ($r->hasFile('photo')) {
            $photoPath = $r->file('photo')->store('avatars', 'public');
        }

        $payload = [
            'name'            => $data['name'],
            'email'           => $data['email'],
            'division_id'     => $data['division_id'] ?? null,
            'role'            => $data['role'],
            'default_site_id' => $data['default_site_id'] ?? null,
            'password'        => Hash::make($plain),
        ];

        if (Schema::hasColumn('users','default_company_id')) {
            $payload['default_company_id'] = $data['default_company_id'] ?? session('company_id');
        }

        if ($photoPath) {
            if (Schema::hasColumn('users','avatar_path')) $payload['avatar_path'] = $photoPath;
            if (Schema::hasColumn('users','photo_path'))  $payload['photo_path']  = $photoPath;
        }

        if (Schema::hasColumn('users','allowed_site_ids')) {
            $payload['allowed_site_ids'] = $data['site_ids'] ?? [];
        }

        $user = User::create($payload);

        try {
            if (method_exists($user, 'sites')) {
                $user->sites()->sync($data['site_ids'] ?? []);
            }
        } catch (\Throwable $e) {}

        return redirect()->route('admin.users.edit', $user)->with([
            'generated_password' => $plain,
            'status' => 'User berhasil dibuat.',
        ]);
    }

    /**
     * FORM EDIT USER
     */
    public function edit(User $user)
    {
        $companyId = session('company_id');

        $user->load(['division','defaultSite','defaultCompany','sites']);

        $companies = Company::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id','code','name']);

        $divisions = Division::query()
            ->when(
                $companyId && Schema::hasColumn('divisions','company_id'),
                function ($qb) use ($companyId) {
                    $qb->where(function ($w) use ($companyId) {
                        $w->where('company_id', $companyId)
                          ->orWhereNull('company_id');
                    });
                }
            )
            ->orderBy('name')
            ->get(['id','name']);

        $sites = Site::query()
            ->when(
                $companyId && Schema::hasColumn('sites','company_id'),
                fn($qb) => $qb->where('company_id', $companyId)
            )
            ->orderBy('code')
            ->get(['id','code','name','region']);

        return view('admin.users.edit', compact('user','companies','divisions','sites','companyId'));
    }

    /**
     * UPDATE USER UTAMA
     */
    public function update(Request $r, User $user): RedirectResponse
    {
        $data = $r->validate([
            'name'               => ['required','string','max:150'],
            'email'              => ['required','email','max:190', Rule::unique('users','email')->ignore($user->id)],

            'default_company_id' => ['nullable','uuid','exists:companies,id'],
            'division_id'        => ['nullable','uuid','exists:divisions,id'],
            'role'               => ['required','in:super_admin,gm,manager,staff,creator'],

            'default_site_id'    => ['nullable','uuid','exists:sites,id'],

            'site_ids'           => ['nullable','array'],
            'site_ids.*'         => ['uuid','exists:sites,id'],

            'password'           => ['nullable','string','min:8'],
        ]);

        $payload = [
            'name'            => $data['name'],
            'email'           => $data['email'],
            'division_id'     => $data['division_id'] ?? null,
            'role'            => $data['role'],
            'default_site_id' => $data['default_site_id'] ?? null,
        ];

        if (Schema::hasColumn('users','default_company_id')) {
            $payload['default_company_id'] = $data['default_company_id'] ?? null;
        }

        if (!empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }

        if (Schema::hasColumn('users','allowed_site_ids')) {
            $payload['allowed_site_ids'] = $data['site_ids'] ?? [];
        }

        $user->update($payload);

        try {
            if (method_exists($user,'sites')) {
                $user->sites()->sync($data['site_ids'] ?? []);
            }
        } catch (\Throwable $e) {}

        return back()->with('status', 'User berhasil diperbarui.');
    }

    public function updateDivision(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'division_id' => ['nullable','uuid', Rule::exists('divisions','id')],
        ]);

        $user->update(['division_id' => $data['division_id'] ?? null]);
        return back()->with('status', 'Division updated.');
    }

    public function updateSite(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'default_site_id' => ['nullable','uuid', Rule::exists('sites','id')],
        ]);

        $user->update(['default_site_id' => $data['default_site_id'] ?? null]);
        return back()->with('status', 'Default site updated.');
    }

    public function resetPassword(User $user): RedirectResponse
    {
        $newPassword = Str::password(14);
        $user->forceFill(['password' => Hash::make($newPassword)])->save();

        return back()->with('generated_password', $newPassword);
    }

    public function updatePhoto(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'photo' => ['required','image','mimes:jpg,jpeg,png','max:2048'],
        ]);

        foreach (['avatar_path','photo_path'] as $col) {
            if (Schema::hasColumn('users',$col) && $user->{$col}) {
                Storage::disk('public')->delete($user->{$col});
            }
        }

        $path = $request->file('photo')->store('avatars','public');

        $fill = [];
        if (Schema::hasColumn('users','avatar_path')) $fill['avatar_path'] = $path;
        if (Schema::hasColumn('users','photo_path'))  $fill['photo_path']  = $path;

        $user->forceFill($fill)->save();

        return back()->with('status','Foto berhasil diperbarui.');
    }

    public function deletePhoto(User $user): RedirectResponse
    {
        foreach (['avatar_path','photo_path'] as $col) {
            if (Schema::hasColumn('users',$col) && $user->{$col}) {
                Storage::disk('public')->delete($user->{$col});
            }
        }

        $fill = [];
        if (Schema::hasColumn('users','avatar_path')) $fill['avatar_path'] = null;
        if (Schema::hasColumn('users','photo_path'))  $fill['photo_path']  = null;

        $user->forceFill($fill)->save();

        return back()->with('status','Foto berhasil dihapus.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('status','Tidak boleh menghapus akun sendiri.');
        }

        // ✅ kalau model pakai SoftDeletes -> delete()
        // ✅ kalau gak -> forceDelete()
        if (in_array(SoftDeletes::class, class_uses_recursive(User::class))) {
            $user->delete();
        } else {
            $user->forceDelete();
        }

        return redirect()->route('admin.users.index')->with('status','User berhasil dihapus.');
    }
}
