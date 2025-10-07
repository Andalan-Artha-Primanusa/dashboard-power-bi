<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Division;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserAdminController extends Controller
{
    /**
     * List users dengan filter sederhana.
     */
    public function index()
    {
        $q        = request('q', '');
        $division = request('division_id');

        $users = User::query()
            ->with(['division', 'defaultSite']) // <-- tampilkan info site di index
            ->when($q, function ($qrb) use ($q) {
                $qrb->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->when($division, fn($qrb) => $qrb->where('division_id', $division))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $divisions = Division::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'divisions', 'q', 'division'));
    }

    /**
     * Form create user.
     */
    public function create()
    {
        $divisions = Division::orderBy('name')->get();
        $sites     = Site::orderBy('code')->get(['id', 'code', 'name', 'region']); // <-- region ikut untuk label

        return view('admin.users.create', compact('divisions', 'sites'));
    }

    /**
     * Simpan user baru (password di-generate & ditampilkan sekali via flash).
     */
    public function store(Request $r): RedirectResponse
    {
        $data = $r->validate([
            'name'            => ['required', 'string', 'max:150'],
            'email'           => ['required', 'email', 'max:190', 'unique:users,email'],
            'division_id'     => ['nullable', 'uuid', 'exists:divisions,id'],
            'role'            => ['nullable', 'string', 'max:50'],
            'default_site_id' => ['nullable', 'uuid', 'exists:sites,id'], // <-- validasi site
        ]);

        $pwdPlain = Str::password(12);

        $user = User::create([
            'name'            => $data['name'],
            'email'           => $data['email'],
            'division_id'     => $data['division_id'] ?? null,
            'role'            => $data['role'] ?? null,
            'default_site_id' => $data['default_site_id'] ?? null,
            'password'        => Hash::make($pwdPlain),
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
                ],
            ], User::class, $user->id);
        }

        return redirect()->route('admin.users.edit', $user)->with([
            'generated_password' => $pwdPlain, // tampil sekali
        ]);
    }

    /**
     * Halaman edit user (aksi cepat: ubah divisi, reset password, hapus).
     */
    public function edit(User $user)
    {
        $divisions = Division::orderBy('name')->get();
        $sites     = Site::orderBy('code')->get(['id', 'code', 'name', 'region']); // <-- untuk tampil & opsi ubah

        return view('admin.users.edit', compact('user', 'divisions', 'sites'));
    }

    /**
     * Hanya update division (aksi terpisah agar aman & ter-audit).
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
}
