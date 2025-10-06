<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserAdminController extends Controller
{
    public function index()
    {
        $users = User::with('division')->orderBy('name')->paginate(20);
        $divisions = Division::orderBy('name')->get();

        return view('admin.users.index', compact('users','divisions'));
    }

    public function edit(User $user)
    {
        $divisions = Division::orderBy('name')->get();
        return view('admin.users.edit', compact('user','divisions'));
    }

    public function updateDivision(Request $request, User $user)
    {
        $data = $request->validate([
            'division_id' => 'nullable|uuid|exists:divisions,id',
        ]);

        $user->update([
            'division_id' => $data['division_id'] ?? null,
        ]);

        return back()->with('status','Division updated.');
    }

    public function resetPassword(Request $request, User $user)
    {
        $newPassword = $request->input('new_password') 
            ?: Str::password(12); // auto generate kalau kosong

        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        // Optional: kirim email notifikasi ke user

        return back()->with('status', "Password reset berhasil: {$newPassword}");
    }
}
