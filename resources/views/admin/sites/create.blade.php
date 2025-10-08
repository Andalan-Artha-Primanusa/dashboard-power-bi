{{-- resources/views/admin/sites/create.blade.php --}}
@extends('layouts.app')

@section('title','Tambah Site')

@section('content')
<div class="max-w-3xl mx-auto rounded-3xl overflow-hidden shadow ring-1 ring-slate-200 bg-white">

  {{-- HEADER (maroon-only • serumpun) --}}
  <div class="px-6 py-7 text-white relative overflow-hidden">
    {{-- Base gradient --}}
    <div class="absolute inset-0 bg-gradient-to-r from-maroon-800 via-maroon-700 to-maroon-600"></div>
    {{-- White sheen --}}
    <div class="absolute inset-0 opacity-25 bg-[radial-gradient(70%_70%_at_10%_10%,_rgba(255,255,255,0.5)_0%,_transparent_60%)]"></div>
    {{-- Soft overlay --}}
    <div class="absolute -top-16 -right-16 size-64 rounded-full bg-white/10 blur-3xl"></div>

    <div class="relative">
      <h1 class="text-2xl font-bold tracking-tight">ARCA</h1>
      <p class="text-white/85 text-sm mt-1">🆕 Tambah Site untuk ERP BISA</p>
    </div>
  </div>

  <form action="{{ route('admin.sites.store') }}" method="POST" class="p-6 space-y-4">
    @csrf
    @include('admin.sites._form')

    <div class="flex items-center justify-end gap-2">
      <a href="{{ route('admin.sites.index') }}"
         class="px-4 py-2 rounded-xl text-sm font-medium text-slate-700 ring-1 ring-slate-200 hover:bg-slate-50">
        Batal
      </a>
      <button
        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-maroon-700 text-white text-sm font-semibold hover:bg-maroon-800 ring-1 ring-maroon-900/20">
        Simpan
      </button>
    </div>
  </form>
</div>
@endsection
