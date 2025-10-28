<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Division;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserAdminController extends Controller
{
    /**
     * List users dengan filter q, division, role, site.
     */
    public function index()
    {
        $q         = request('q', '');
        $division  = request('division_id');
        $roleKey   = request('role');
        $siteId    = request('site_id');

        $users = User::query()
            ->with(['division', 'defaultSite'])
            ->when($q, function ($qrb) use ($q) {
                $qrb->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->when($division, fn($qrb) => $qrb->where('division_id', $division))
            ->when($roleKey, fn($qrb) => $qrb->where('role', $roleKey))
            ->when($siteId,  fn($qrb) => $qrb->where('default_site_id', $siteId))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $divisions = Division::orderBy('name')->get();
        $sites     = Site::orderBy('code')->get(['id','code','name','region']);

        // mapping role sederhana agar sesuai dengan <select> di view
        $roles = [
            'super_admin' => 'Super Admin',
            'gm'          => 'GM',
            'manager'     => 'Manager',
            'staff'       => 'Staff',
        ];

        return view('admin.users.index', compact('users','divisions','sites','roles','q','division','roleKey','siteId'));
    }

    /**
     * Form create user.
     */
    public function create()
    {
        $divisions = Division::orderBy('name')->get();
        $sites     = Site::orderBy('code')->get(['id', 'code', 'name', 'region']);

        return view('admin.users.create', compact('divisions', 'sites'));
    }

    /**
     * Simpan user baru (password bisa diisi, default generate) + simpan foto jika ada.
     */
    public function store(Request $r): RedirectResponse
    {
        $data = $r->validate([
            'name'             => ['required', 'string', 'max:150'],
            'email'            => ['required', 'email', 'max:190', 'unique:users,email'],
            'division_id'      => ['nullable', 'uuid', 'exists:divisions,id'],
            'role'             => ['required', 'in:super_admin,gm,manager,staff'],
            'default_site_id'  => ['nullable', 'uuid', 'exists:sites,id'],
            'password'         => ['nullable', 'string', 'min:8'],
            'photo'            => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $plain = $data['password'] ?? Str::password(12);

        // handle foto
        $avatarPath = null;
        if ($r->hasFile('photo')) {
            $avatarPath = $r->file('photo')->store('avatars', 'public');
        }

        $user = User::create([
            'name'             => $data['name'],
            'email'            => $data['email'],
            'division_id'      => $data['division_id'] ?? null,
            'role'             => $data['role'],
            'default_site_id'  => $data['default_site_id'] ?? null,
            'password'         => Hash::make($plain),
            'avatar_path'      => $avatarPath, // pastikan kolom ada di tabel users
        ]);

        if (function_exists('audit')) {
            audit('user.create', [
                'target_user_id'    => $user->id,
                'target_user_email' => $user->email,
                'by_user_id'        => auth()->id(),
                'payload'           => [
                    'division_id'     => $user->division_id,
                    'role'            => $user->role,
                    'default_site_id' => $user->default_site_id,
                    'has_avatar'      => (bool)$avatarPath,
                ],
            ], User::class, $user->id);
        }

        // Redirect ke edit agar banner password tampil (sekali)
        return redirect()->route('admin.users.edit', $user)->with([
            'generated_password' => $plain,
            'status'             => 'User berhasil dibuat.',
        ]);
    }

    /**
     * Halaman edit user.
     */
    public function edit(User $user)
    {
        $divisions = Division::orderBy('name')->get();
        $sites     = Site::orderBy('code')->get(['id', 'code', 'name', 'region']);

        return view('admin.users.edit', compact('user', 'divisions', 'sites'));
    }

    /**
     * Update division.
     */
    public function updateDivision(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'division_id' => ['nullable', 'uuid', Rule::exists('divisions', 'id')],
        ]);

        $before = [
            'division_id'   => $user->getOriginal('division_id'),
            'division_name' => optional($user->division)->name,
        ];

        $user->update(['division_id' => $data['division_id'] ?? null]);
        $user->load('division');

        $after = [
            'division_id'   => $user->division_id,
            'division_name' => optional($user->division)->name,
        ];

        if (function_exists('audit')) {
            audit('user.update_division', [
                'target_user_id'   => $user->id,
                'target_user_name' => $user->name,
                'before'           => $before,
                'after'            => $after,
            ], User::class, $user->id);
        }

        return back()->with('status', 'Division updated.');
    }

    /**
     * Update default site.
     */
    public function updateSite(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'default_site_id' => ['nullable', 'uuid', Rule::exists('sites', 'id')],
        ]);

        $before = $user->getOriginal('default_site_id');
        $user->update(['default_site_id' => $data['default_site_id'] ?? null]);
        $after  = $user->default_site_id;

        if (function_exists('audit')) {
            audit('user.update_default_site', [
                'target_user_id'   => $user->id,
                'target_user_name' => $user->name,
                'before'           => $before,
                'after'            => $after,
            ], User::class, $user->id);
        }

        return back()->with('status', 'Default site updated.');
    }

    /**
     * Reset password: auto-generate & flash plaintext sekali.
     */
    public function resetPassword(User $user): RedirectResponse
    {
        $newPassword = Str::password(14);

        $user->forceFill(['password' => Hash::make($newPassword)])->save();

        if (function_exists('audit')) {
            audit('user.reset_password_generated', [
                'target_user_id'    => $user->id,
                'target_user_email' => $user->email,
                'by_user_id'        => auth()->id(),
                'note'              => 'Password regenerated by admin. Plaintext not stored.',
            ], User::class, $user->id);
        }

        return back()->with('generated_password', $newPassword);
    }

    /**
     * Upload/replace foto avatar user.
     */
    public function updatePhoto(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        // Hapus avatar lama (jika ada)
        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $path = $request->file('photo')->store('avatars', 'public');

        $user->forceFill([
            'avatar_path' => $path,
            // jika pakai field lain, bisa dikosongkan:
            // 'photo_url' => null,
            // 'profile_photo_path' => null,
        ])->save();

        if (function_exists('audit')) {
            audit('user.update_photo', [
                'target_user_id'   => $user->id,
                'target_user_name' => $user->name,
                'by_user_id'       => auth()->id(),
                'path'             => $path,
            ], User::class, $user->id);
        }

        return back()->with('status', 'Foto berhasil diperbarui.');
    }

    /**
     * Hapus foto avatar user.
     */
    public function deletePhoto(User $user): RedirectResponse
    {
        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $user->forceFill([
            'avatar_path' => null,
            // 'photo_url' => null,
            // 'profile_photo_path' => null,
        ])->save();

        if (function_exists('audit')) {
            audit('user.delete_photo', [
                'target_user_id'   => $user->id,
                'target_user_name' => $user->name,
                'by_user_id'       => auth()->id(),
            ], User::class, $user->id);
        }

        return back()->with('status', 'Foto berhasil dihapus.');
    }

    /**
     * Soft delete user.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('status', 'Tidak boleh menghapus akun sendiri.');
        }

        $user->delete(); // soft delete

        if (function_exists('audit')) {
            audit('user.delete', [
                'target_user_id'    => $user->id,
                'target_user_email' => $user->email,
                'by_user_id'        => auth()->id(),
            ], User::class, $user->id);
        }

        return redirect()->route('admin.users.index')->with('status', 'User berhasil dihapus.');
    }
}
