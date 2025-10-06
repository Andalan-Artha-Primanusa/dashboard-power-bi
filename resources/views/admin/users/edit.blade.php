@extends('layouts.app')

@section('title', 'Edit User')

@section('header')
    ✏️ Edit User: {{ $user->name }}
@endsection

@section('content')
<div class="bg-white shadow rounded-lg p-6 space-y-6">
    {{-- Update Division --}}
    <form method="POST" action="{{ route('admin.users.updateDivision',$user) }}">
        @csrf
        @method('PATCH')

        <label class="block mb-2 font-medium">Division</label>
        <select name="division_id" class="border rounded w-full">
            <option value="">— None —</option>
            @foreach($divisions as $d)
                <option value="{{ $d->id }}" @selected($user->division_id==$d->id)>
                    {{ $d->name }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="mt-3 px-4 py-2 bg-maroon-700 text-white rounded">Update Division</button>
    </form>

    {{-- Reset Password --}}
    <form method="POST" action="{{ route('admin.users.resetPassword',$user) }}" class="mt-6">
        @csrf
        <label class="block mb-2 font-medium">New Password (opsional)</label>
        <input type="text" name="new_password" class="border rounded w-full">
        <p class="text-xs text-gray-500">Kosongkan untuk generate random password.</p>

        <button type="submit" class="mt-3 px-4 py-2 bg-emerald-600 text-white rounded">Reset Password</button>
    </form>
</div>
@endsection
