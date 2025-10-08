{{-- resources/views/admin/users/create.blade.php --}}
@extends('layouts.app')

@section('title','Tambah User')

@section('content')
<div class="max-w-3xl mx-auto rounded-3xl overflow-hidden shadow ring-1 ring-slate-200 bg-white">

  {{-- HEADER (maroon-only konsisten ARCA) --}}
  <div class="px-6 py-7 text-white relative overflow-hidden">
    {{-- Base gradient --}}
    <div class="absolute inset-0 bg-gradient-to-r from-maroon-800 via-maroon-700 to-maroon-600"></div>
    {{-- White sheen --}}
    <div class="absolute inset-0 opacity-25 bg-[radial-gradient(70%_70%_at_10%_10%,_rgba(255,255,255,0.5)_0%,_transparent_60%)]"></div>
    {{-- Soft overlay --}}
    <div class="absolute -top-16 -right-16 size-64 rounded-full bg-white/10 blur-3xl"></div>

    <div class="relative">
      <h1 class="text-2xl font-bold tracking-tight">➕ Tambah User</h1>
      <p class="text-sm text-white/85 mt-1">Buat akun user baru untuk ERP BISA.</p>
    </div>
  </div>

  {{-- FORM --}}
  <form action="{{ route('admin.users.store') }}" method="POST" class="p-6 space-y-5">
    @csrf

    <div class="grid sm:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-semibold text-slate-700">Nama</label>
        <input type="text" name="name" value="{{ old('name') }}" required
               class="mt-1 block w-full rounded-xl border-slate-300 focus:ring-maroon-700 focus:border-maroon-700">
        @error('name') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-semibold text-slate-700">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required
               class="mt-1 block w-full rounded-xl border-slate-300 focus:ring-maroon-700 focus:border-maroon-700">
        @error('email') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-semibold text-slate-700">Divisi</label>
        <select name="division_id"
                class="mt-1 block w-full rounded-xl border-slate-300 focus:ring-maroon-700 focus:border-maroon-700">
          <option value="">— Pilih Divisi —</option>
          @foreach($divisions as $d)
            <option value="{{ $d->id }}" @selected(old('division_id') == $d->id)>{{ $d->name }}</option>
          @endforeach
        </select>
        @error('division_id') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-semibold text-slate-700">Role</label>
        <select name="role"
                class="mt-1 block w-full rounded-xl border-slate-300 focus:ring-maroon-700 focus:border-maroon-700">
          <option value="">— Pilih Role —</option>
          <option value="super_admin" @selected(old('role')==='super_admin')>Super Admin</option>
          <option value="gm"          @selected(old('role')==='gm')>General Manager</option>
          <option value="manager"     @selected(old('role')==='manager')>Manager</option>
          <option value="staff"       @selected(old('role')==='staff')>Staff</option>
        </select>
        @error('role') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- Default Site --}}
      <div class="sm:col-span-2">
        <label class="block text-sm font-semibold text-slate-700">Default Site</label>
        <select name="default_site_id"
                class="mt-1 block w-full rounded-xl border-slate-300 focus:ring-maroon-700 focus:border-maroon-700">
          <option value="">— Tanpa Default Site —</option>
          @foreach($sites as $s)
            <option value="{{ $s->id }}" @selected(old('default_site_id') == $s->id)>
              {{ $s->code }} — {{ $s->name }}
            </option>
          @endforeach
        </select>
        <p class="text-xs text-slate-500 mt-1">
          Non-GM/non-Super Admin akan otomatis terkunci ke default site ini saat login.
        </p>
        @error('default_site_id') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
      </div>
    </div>

    {{-- Actions --}}
    <div class="pt-4 flex items-center justify-between gap-3">
      <a href="{{ route('admin.users.index') }}"
         class="px-4 py-2 rounded-xl text-sm font-medium text-slate-700 ring-1 ring-slate-200 hover:bg-slate-50">
        ← Kembali
      </a>
      <button type="submit"
              class="px-4 py-2 rounded-xl bg-maroon-700 text-white font-semibold hover:bg-maroon-800 ring-1 ring-maroon-900/20">
        💾 Simpan
      </button>
    </div>
  </form>
</div>
@endsection
