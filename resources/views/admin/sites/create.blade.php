{{-- resources/views/admin/sites/create.blade.php --}}
@extends('layouts.app')

@section('title','Tambah Site')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

  {{-- HEADER MAROON (seragam ARCA) --}}
  <div class="relative overflow-hidden rounded-3xl ring-1 ring-white/40 bg-gradient-to-r from-maroon-800 via-maroon-700 to-maroon-600">
    {{-- radial highlight --}}
    <div class="absolute inset-0 opacity-25 bg-[radial-gradient(70%_70%_at_10%_10%,_rgba(255,255,255,0.5)_0%,_transparent_60%)]"></div>
    {{-- soft blob --}}
    <div class="absolute -top-16 -right-16 size-64 rounded-full bg-white/10 blur-3xl"></div>

    <div class="relative px-6 py-6 sm:px-8 sm:py-7 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 text-white">
      <div class="space-y-1">
        <div class="inline-flex items-center gap-2 text-[11px] font-semibold text-white/85 uppercase tracking-wide">
          <span class="inline-flex h-6 w-6 items-center justify-center rounded-xl bg-white/20 ring-1 ring-white/30">
            <img src="{{ asset('assets/images/logoarca.png') }}" alt="ARCA" class="h-4 w-4 object-contain">
          </span>
          <span>ARCA — Master Data</span>
        </div>
        <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight">
          🆕 Tambah Site
        </h1>
        <p class="text-xs sm:text-sm text-white/80 max-w-xl">
          Tambahkan site/cabang baru untuk ERP BISA dan modul lainnya.
        </p>
      </div>

      <div class="flex flex-wrap gap-2 justify-start sm:justify-end">
        <a href="{{ route('admin.sites.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 text-white text-sm font-semibold hover:bg-white/20 ring-1 ring-white/40">
          <span class="text-lg leading-none">←</span>
          <span>Kembali ke Sites</span>
        </a>
      </div>
    </div>
  </div>

  {{-- FORM CARD --}}
  <form action="{{ route('admin.sites.store') }}" method="POST"
        class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm ring-1 ring-slate-100/80">
    @csrf

    {{-- accent bar kiri --}}
    <div class="absolute inset-y-0 left-0 w-1.5 bg-gradient-to-b from-maroon-700 via-maroon-600 to-maroon-700"></div>

    <div class="px-6 py-6 sm:px-8 sm:py-7 space-y-6">
      {{-- header kecil --}}
      <div class="flex items-center justify-between gap-3 pb-3 border-b border-slate-100">
        <div>
          <h2 class="text-sm font-semibold tracking-wide text-slate-700 uppercase">
            Detail Site
          </h2>
          <p class="text-xs text-slate-500 mt-0.5">
            Isi informasi site yang akan dipakai di berbagai modul ARCA.
          </p>
        </div>
      </div>

      {{-- FIELDS dari partial --}}
      <div class="space-y-4">
        @include('admin.sites._form')
      </div>

      {{-- ACTIONS --}}
      <div class="pt-3 flex items-center justify-end gap-2 border-t border-slate-100">
        <a href="{{ route('admin.sites.index') }}"
           class="px-4 py-2 rounded-xl text-sm font-medium text-slate-700 ring-1 ring-slate-200 hover:bg-slate-50">
          Batal
        </a>
        <button
          class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-maroon-700 text-white text-sm font-semibold hover:bg-maroon-800 ring-1 ring-maroon-900/20">
          Simpan
        </button>
      </div>
    </div>
  </form>
</div>
@endsection
