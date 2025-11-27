{{-- resources/views/admin/powerbi/edit.blade.php --}}
@extends('layouts.app')

@section('title','Edit Power BI')

@section('content')
@php
  // Pastikan variabel: $report, $users, $divisions, $sites, $selectedUsers, $selectedDivs, $selectedSites
@endphp

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
          <span>ARCA — Power BI</span>
        </div>
        <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight">
          ✏️ Edit Power BI Report
        </h1>
        <p class="text-xs sm:text-sm text-white/80 max-w-xl">
          Perbarui metadata laporan dan pengaturan hak akses embed.
        </p>
      </div>

      <div class="flex flex-wrap gap-2 justify-start sm:justify-end">
        <a href="{{ route('admin.powerbi.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 text-white text-sm font-semibold hover:bg-white/20 ring-1 ring-white/40">
          <span class="text-lg leading-none">←</span>
          <span>Kembali ke daftar</span>
        </a>
      </div>
    </div>
  </div>

  {{-- ALERT ERROR --}}
  @if ($errors->any())
    <div class="relative rounded-2xl border border-rose-200 bg-rose-50/90 ring-1 ring-rose-100 shadow-sm">
      <div class="flex items-start gap-3 p-4">
        <svg class="h-5 w-5 text-rose-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-width="2" d="M12 9v4m0 4h.01"/>
          <path stroke-width="2" d="M12 2a10 10 0 100 20 10 10 0 000-20z"/>
        </svg>
        <div class="text-sm text-rose-900">
          <div class="font-semibold mb-1">Form masih ada error:</div>
          <ul class="list-disc ml-5 space-y-0.5">
            @foreach ($errors->all() as $e)
              <li>{{ $e }}</li>
            @endforeach
          </ul>
        </div>
      </div>
      <div class="h-1 w-full bg-gradient-to-r from-rose-500 via-rose-400 to-rose-600"></div>
    </div>
  @endif

  <form method="POST" action="{{ route('admin.powerbi.update', $report) }}" class="space-y-6">
    @csrf
    @method('PUT')

    {{-- METADATA CARD --}}
    <div class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm ring-1 ring-slate-100/80">
      {{-- accent bar kiri --}}
      <div class="absolute inset-y-0 left-0 w-1.5 bg-gradient-to-b from-maroon-700 via-maroon-600 to-maroon-700"></div>

      <div class="px-6 py-6 sm:px-8 sm:py-7 space-y-6">
        {{-- header kecil --}}
        <div class="flex items-center justify-between gap-3 pb-3 border-b border-slate-100">
          <div>
            <h2 class="text-sm font-semibold tracking-wide text-slate-700 uppercase">
              Detail Laporan
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">
              Nama laporan dan URL embed dari Power BI.
            </p>
          </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
          <label class="block">
            <span class="block text-sm font-medium text-slate-700">Nama</span>
            <input name="name" value="{{ old('name',$report->name) }}" required
                   class="mt-1 w-full rounded-2xl border border-sky-200 bg-white
                          px-4 py-2.5 text-sm text-slate-700 shadow-sm
                          focus:outline-none focus:ring-2 focus:ring-sky-300/70 focus:border-sky-400"/>
            @error('name')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
          </label>

          <label class="block">
            <span class="block text-sm font-medium text-slate-700">Embed URL</span>
            <input name="embed_url" type="url" value="{{ old('embed_url',$report->embed_url) }}" required
                   class="mt-1 w-full rounded-2xl border border-sky-200 bg-white
                          px-4 py-2.5 text-sm text-slate-700 shadow-sm
                          focus:outline-none focus:ring-2 focus:ring-sky-300/70 focus:border-sky-400"/>
            @error('embed_url')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
          </label>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3">
          <label class="inline-flex items-center gap-2 text-sm text-slate-700">
            <input type="checkbox" name="show_filter_pane" value="1"
                   class="rounded border-slate-300 text-maroon-700 focus:ring-maroon-600/30"
                   @checked(old('show_filter_pane', $report->show_filter_pane))>
            <span>Filter Pane</span>
          </label>
          <label class="inline-flex items-center gap-2 text-sm text-slate-700">
            <input type="checkbox" name="show_nav_pane" value="1"
                   class="rounded border-slate-300 text-maroon-700 focus:ring-maroon-600/30"
                   @checked(old('show_nav_pane', $report->show_nav_pane))>
            <span>Nav Pane</span>
          </label>
          <label class="inline-flex items-center gap-2 text-sm text-slate-700">
            <input type="checkbox" name="show_toolbar" value="1"
                   class="rounded border-slate-300 text-maroon-700 focus:ring-maroon-600/30"
                   @checked(old('show_toolbar', $report->show_toolbar))>
            <span>Toolbar</span>
          </label>
          <label class="inline-flex items-center gap-2 text-sm text-slate-700">
            <input type="checkbox" name="allow_client_download" value="1"
                   class="rounded border-slate-300 text-maroon-700 focus:ring-maroon-600/30"
                   @checked(old('allow_client_download', $report->allow_client_download))>
            <span>Client Download</span>
          </label>
        </div>
      </div>
    </div>

    {{-- GRANTS CARD --}}
    <div class="rounded-3xl border border-slate-200 bg-white shadow-sm ring-1 ring-slate-100/80 p-6 sm:p-7 grid md:grid-cols-3 gap-6">
      {{-- Users --}}
      <div>
        <h3 class="font-semibold mb-2 text-slate-800 text-sm uppercase tracking-wide">Bagikan ke User</h3>
        <div class="max-h-64 overflow-auto space-y-1 pr-1">
          @foreach($users as $u)
            <label class="flex items-center gap-2 text-sm">
              <input type="checkbox" name="user_ids[]" value="{{ $u->id }}"
                     class="rounded border-slate-300 text-maroon-700 focus:ring-maroon-600/30"
                     @checked(in_array($u->id, old('user_ids',$selectedUsers ?? [])))>
              <span class="truncate">{{ $u->name }}</span>
              <span class="text-slate-500 truncate">({{ $u->email }})</span>
            </label>
          @endforeach
        </div>
        @error('user_ids')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
      </div>

      {{-- Divisions --}}
      <div>
        <h3 class="font-semibold mb-2 text-slate-800 text-sm uppercase tracking-wide">Bagikan ke Divisi</h3>
        <div class="max-h-64 overflow-auto space-y-1 pr-1">
          @foreach($divisions as $d)
            <label class="flex items-center gap-2 text-sm">
              <input type="checkbox" name="division_ids[]" value="{{ $d->id }}"
                     class="rounded border-slate-300 text-maroon-700 focus:ring-maroon-600/30"
                     @checked(in_array($d->id, old('division_ids',$selectedDivs ?? [])))>
              <span class="truncate">{{ $d->name }}</span>
            </label>
          @endforeach
        </div>
        @error('division_ids')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
      </div>

      {{-- Sites --}}
      <div>
        <h3 class="font-semibold mb-2 text-slate-800 text-sm uppercase tracking-wide">Bagikan ke Site</h3>
        <div class="max-h-64 overflow-auto space-y-1 pr-1">
          @foreach($sites as $s)
            <label class="flex items-center gap-2 text-sm">
              <input type="checkbox" name="site_ids[]" value="{{ $s->id }}"
                     class="rounded border-slate-300 text-maroon-700 focus:ring-maroon-600/30"
                     @checked(in_array($s->id, old('site_ids',$selectedSites ?? [])))>
            <span class="truncate">{{ $s->code }} — {{ $s->name }}</span>
            </label>
          @endforeach
        </div>
        @error('site_ids')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
      </div>
    </div>

    {{-- ACTIONS --}}
    <div class="flex items-center gap-3">
      <button class="px-5 py-2.5 rounded-xl bg-maroon-700 text-white text-sm font-semibold hover:bg-maroon-800 shadow">
        Simpan Perubahan
      </button>
      <a href="{{ route('admin.powerbi.index') }}"
         class="px-5 py-2.5 rounded-xl ring-1 ring-slate-300 text-sm text-slate-700 bg-white hover:bg-slate-50">
        Batal
      </a>
    </div>
  </form>
</div>
@endsection
