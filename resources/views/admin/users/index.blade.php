@extends('layouts.app')

@section('title', 'Manajemen User')

@section('header')
    👥 Manajemen User
@endsection

@section('content')
<div class="bg-white shadow rounded-lg p-6 space-y-6">
    <table class="w-full text-sm text-left">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2">Nama</th>
                <th class="px-4 py-2">Email</th>
                <th class="px-4 py-2">Divisi</th>
                <th class="px-4 py-2">Role</th>
                <th class="px-4 py-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $u)
                <tr class="border-b">
                    <td class="px-4 py-2">{{ $u->name }}</td>
                    <td class="px-4 py-2">{{ $u->email }}</td>
                    <td class="px-4 py-2">{{ $u->division->name ?? '-' }}</td>
                    <td class="px-4 py-2">{{ $u->role }}</td>
                    <td class="px-4 py-2">
                        <a href="{{ route('admin.users.edit',$u) }}" 
                           class="text-emerald-600 hover:underline">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">{{ $users->links() }}</div>
</div>
@endsection
