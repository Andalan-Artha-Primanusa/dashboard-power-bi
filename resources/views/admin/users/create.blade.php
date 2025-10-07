{{-- resources/views/admin/users/create.blade.php --}}
@extends('layouts.app')

@section('title','Tambah User')

@section('content')
<div class="rounded-3xl shadow ring-1 ring-slate-200 bg-white overflow-hidden">
  {{-- HEADER --}}
  <div class="px-6 py-6 bg-gradient-to-r from-maroon-700 via-maroon-600 to-yellow-600">
    <h1 class="text-2xl font-bold text-white">➕ Tambah User</h1>
    <p class="text-sm text-white/85 mt-1">Buat akun user baru untuk ERP.</p>
  </div>

  {{-- FORM --}}
  <form action="{{ route('admin.users.store') }}" method="POST" class="p-6 space-y-5">
    @csrf

    <div class="grid sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-semibold text-slate-700">Nama</label>
        <input type="text" name="name" value="{{ old('name') }}"
               class="mt-1 block w-full rounded-lg border-slate-300 focus:ring-gold-500 focus:border-gold-500" required>
        @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-semibold text-slate-700">Email</label>
        <input type="email" name="email" value="{{ old('email') }}"
               class="mt-1 block w-full rounded-lg border-slate-300 focus:ring-gold-500 focus:border-gold-500" required>
        @error('email') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-semibold text-slate-700">Divisi</label>
        <select name="division_id"
                class="mt-1 block w-full rounded-lg border-slate-300 focus:ring-gold-500 focus:border-gold-500">
          <option value="">— Pilih Divisi —</option>
          @foreach($divisions as $d)
            <option value="{{ $d->id }}" @selected(old('division_id')===$d->id)>{{ $d->name }}</option>
          @endforeach
        </select>
        @error('division_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-semibold text-slate-700">Role</label>
        <select name="role"
                class="mt-1 block w-full rounded-lg border-slate-300 focus:ring-gold-500 focus:border-gold-500">
          <option value="">— Pilih Role —</option>
          <option value="super_admin" @selected(old('role')==='super_admin')>Super Admin</option>
          <option value="gm"          @selected(old('role')==='gm')>General Manager</option>
          <option value="manager"     @selected(old('role')==='manager')>Manager</option>
          <option value="staff"       @selected(old('role')==='staff')>Staff</option>
        </select>
        @error('role') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- === Default Site (BARU) === --}}
      <div class="sm:col-span-2">
        <label class="block text-sm font-semibold text-slate-700">Default Site</label>
        <select name="default_site_id"
                class="mt-1 block w-full rounded-lg border-slate-300 focus:ring-gold-500 focus:border-gold-500">
          <option value="">— Tanpa Default Site —</option>
          @foreach($sites as $s)
            <option value="{{ $s->id }}" @selected(old('default_site_id')===$s->id)>
              {{ $s->code }} — {{ $s->name }}
            </option>
          @endforeach
        </select>
        <p class="text-xs text-slate-500 mt-1">
          Non-GM/non-Super Admin akan otomatis terkunci ke default site ini saat login.
        </p>
        @error('default_site_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
      </div>
      {{-- === /Default Site === --}}

    </div>

    <div class="pt-2">
      <button type="submit" class="px-4 py-2 rounded-xl bg-gold-500 text-maroon-900 font-bold hover:bg-gold-400">
        Simpan
      </button>
      <a href="{{ route('admin.users.index') }}" class="ml-2 text-slate-600 hover:underline">Batal</a>
    </div>
  </form>
</div>
@endsection
