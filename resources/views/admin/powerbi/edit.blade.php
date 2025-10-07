{{-- resources/views/admin/powerbi/edit.blade.php --}}
@extends('layouts.app')

@section('title','Edit Power BI')

@section('content')
@php
  // Pastikan variabel: $report, $users, $divisions, $sites, $selectedUsers, $selectedDivs, $selectedSites
@endphp

<form method="POST" action="{{ route('admin.powerbi.update', $report) }}" class="space-y-6 max-w-4xl">
  @csrf
  @method('PUT')

  {{-- HEADER CARD --}}
  <div class="rounded-2xl ring-1 ring-slate-200 overflow-hidden">
    <div class="px-6 py-5 bg-gradient-to-r from-maroon-700 via-maroon-600 to-yellow-600 text-white">
      <div class="flex items-center justify-between gap-3">
        <div>
          <h1 class="text-xl font-bold">✏️ Edit Power BI Report</h1>
          <p class="text-white/85 text-sm">Perbarui metadata & pengaturan embed.</p>
        </div>
        <a href="{{ route('admin.powerbi.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white text-maroon-800 font-semibold hover:bg-white/90 shadow-sm">
          ← Kembali
        </a>
      </div>
    </div>

    {{-- FORM BODY --}}
    <div class="p-6 grid gap-6">
      <div class="grid md:grid-cols-2 gap-6">
        <label class="block">
          <span class="block text-sm font-medium text-slate-700">Nama</span>
          <input name="name" value="{{ old('name',$report->name) }}" required
                 class="mt-1 w-full rounded-xl border-slate-300 focus:ring-gold-500 focus:border-gold-500"/>
          @error('name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
        </label>

        <label class="block">
          <span class="block text-sm font-medium text-slate-700">Embed URL</span>
          <input name="embed_url" type="url" value="{{ old('embed_url',$report->embed_url) }}" required
                 class="mt-1 w-full rounded-xl border-slate-300 focus:ring-gold-500 focus:border-gold-500"/>
          @error('embed_url')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
        </label>
      </div>

      <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <label class="inline-flex items-center gap-2">
          <input type="checkbox" name="show_filter_pane" value="1"
                 @checked(old('show_filter_pane', $report->show_filter_pane))>
          <span class="text-sm text-slate-700">Filter Pane</span>
        </label>
        <label class="inline-flex items-center gap-2">
          <input type="checkbox" name="show_nav_pane" value="1"
                 @checked(old('show_nav_pane', $report->show_nav_pane))>
          <span class="text-sm text-slate-700">Nav Pane</span>
        </label>
        <label class="inline-flex items-center gap-2">
          <input type="checkbox" name="show_toolbar" value="1"
                 @checked(old('show_toolbar', $report->show_toolbar))>
          <span class="text-sm text-slate-700">Toolbar</span>
        </label>
        <label class="inline-flex items-center gap-2">
          <input type="checkbox" name="allow_client_download" value="1"
                 @checked(old('allow_client_download', $report->allow_client_download))>
          <span class="text-sm text-slate-700">Client Download</span>
        </label>
      </div>
    </div>
  </div>

  {{-- GRANTS CARD --}}
  <div class="rounded-2xl ring-1 ring-slate-200 p-6 grid md:grid-cols-3 gap-6 bg-white">
    {{-- Users --}}
    <div>
      <h3 class="font-semibold mb-2 text-slate-800">Bagikan ke User</h3>
      <div class="max-h-64 overflow-auto space-y-1 pr-1">
        @foreach($users as $u)
          <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="user_ids[]" value="{{ $u->id }}"
              @checked(in_array($u->id, old('user_ids',$selectedUsers ?? [])))>
            <span class="truncate">{{ $u->name }}</span>
            <span class="text-slate-500 truncate">({{ $u->email }})</span>
          </label>
        @endforeach
      </div>
      @error('user_ids')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>

    {{-- Divisions --}}
    <div>
      <h3 class="font-semibold mb-2 text-slate-800">Bagikan ke Divisi</h3>
      <div class="max-h-64 overflow-auto space-y-1 pr-1">
        @foreach($divisions as $d)
          <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="division_ids[]" value="{{ $d->id }}"
              @checked(in_array($d->id, old('division_ids',$selectedDivs ?? [])))>
            <span class="truncate">{{ $d->name }}</span>
          </label>
        @endforeach
      </div>
      @error('division_ids')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>

    {{-- Sites --}}
    <div>
      <h3 class="font-semibold mb-2 text-slate-800">Bagikan ke Site</h3>
      <div class="max-h-64 overflow-auto space-y-1 pr-1">
        @foreach($sites as $s)
          <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="site_ids[]" value="{{ $s->id }}"
              @checked(in_array($s->id, old('site_ids',$selectedSites ?? [])))>
            <span class="truncate">{{ $s->code }} — {{ $s->name }}</span>
          </label>
        @endforeach
      </div>
      @error('site_ids')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>
  </div>

  {{-- ACTIONS --}}
  <div class="flex items-center gap-3">
    <button class="px-5 py-2.5 rounded-xl bg-emerald-600 text-white font-semibold hover:bg-emerald-500">
      Simpan Perubahan
    </button>
    <a href="{{ route('admin.powerbi.index') }}" class="px-5 py-2.5 rounded-xl ring-1 ring-slate-300 text-slate-700 bg-white hover:bg-slate-50">
      Batal
    </a>
  </div>
</form>
@endsection
