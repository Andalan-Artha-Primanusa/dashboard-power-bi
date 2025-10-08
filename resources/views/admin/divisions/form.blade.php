@extends('layouts.app')

@section('title', $division->exists ? 'Edit Divisi' : 'Tambah Divisi')

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
      <h1 class="text-2xl font-bold tracking-tight">
        {{ $division->exists ? '✏️ Edit Divisi' : '➕ Tambah Divisi' }}
      </h1>
      <p class="text-white/85 text-sm mt-1">
        {{ $division->exists ? 'Perbarui informasi Divisi.' : 'Daftarkan Divisi baru untuk ERP BISA.' }}
      </p>
    </div>
  </div>

  {{-- FORM --}}
  <form method="POST"
        action="{{ $division->exists ? route('admin.divisions.update',$division) : route('admin.divisions.store') }}"
        class="p-6 space-y-5">
    @csrf
    @if($division->exists) @method('PUT') @endif

    <div class="space-y-2">
      <label class="block text-sm font-medium text-slate-700">Nama Divisi</label>
      <input type="text" name="name" value="{{ old('name',$division->name) }}" required
             class="w-full rounded-xl border-slate-300 focus:ring-maroon-700 focus:border-maroon-700">
      @error('name') <p class="text-sm text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div class="space-y-2">
      <label class="block text-sm font-medium text-slate-700">Kode</label>
      <input type="text" name="code" value="{{ old('code',$division->code) }}" required
             class="w-full rounded-xl border-slate-300 focus:ring-maroon-700 focus:border-maroon-700">
      @error('code') <p class="text-sm text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div class="space-y-2">
      <label class="block text-sm font-medium text-slate-700">Deskripsi</label>
      <textarea name="description" rows="3"
                class="w-full rounded-xl border-slate-300 focus:ring-maroon-700 focus:border-maroon-700">{{ old('description',$division->description) }}</textarea>
      @error('description') <p class="text-sm text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div class="flex items-center gap-2">
      <input type="checkbox" name="is_active" value="1"
             class="rounded border-slate-300 text-maroon-700 focus:ring-maroon-600"
             {{ old('is_active',$division->is_active) ? 'checked' : '' }}>
      <label class="text-sm font-medium text-slate-700">Aktif</label>
    </div>

    <div class="flex items-center justify-between gap-3 pt-4">
      <a href="{{ route('admin.divisions.index') }}"
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
