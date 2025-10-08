@extends('layouts.app')
@section('title','Tambah Power BI')

@section('content')
<div class="max-w-4xl mx-auto rounded-3xl overflow-hidden shadow ring-1 ring-slate-200 bg-white">

  {{-- HEADER (maroon only, konsisten ARCA) --}}
  <div class="px-6 py-7 text-white relative overflow-hidden">
    {{-- Base gradient --}}
    <div class="absolute inset-0 bg-gradient-to-r from-maroon-800 via-maroon-700 to-maroon-600"></div>
    {{-- White sheen --}}
    <div class="absolute inset-0 opacity-25 bg-[radial-gradient(70%_70%_at_10%_10%,_rgba(255,255,255,0.5)_0%,_transparent_60%)]"></div>
    {{-- Soft overlay --}}
    <div class="absolute -top-16 -right-16 size-64 rounded-full bg-white/10 blur-3xl"></div>

    <div class="relative">
      <h1 class="text-2xl font-bold tracking-tight">ARCA</h1>
      <p class="text-white/85 text-sm mt-1">🆕 Tambah Report Power BI baru</p>
    </div>
  </div>

  {{-- FORM --}}
  <form method="POST" action="{{ route('admin.powerbi.store') }}" class="p-6 space-y-6">
    @csrf

    {{-- Informasi utama --}}
    <div class="p-5 rounded-2xl ring-1 ring-slate-200 space-y-4 bg-slate-50/40">
      <label class="block text-sm font-medium text-slate-700">
        Nama
        <input name="name" class="mt-1 w-full rounded-xl border-slate-300 focus:ring-maroon-700 focus:border-maroon-700" required>
      </label>
      <label class="block text-sm font-medium text-slate-700">
        Embed URL
        <input name="embed_url" type="url" class="mt-1 w-full rounded-xl border-slate-300 focus:ring-maroon-700 focus:border-maroon-700" required>
      </label>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-3 pt-2">
        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
          <input type="checkbox" name="show_filter_pane" value="1" class="rounded text-maroon-700 focus:ring-maroon-600">
          Filter Pane
        </label>
        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
          <input type="checkbox" name="show_nav_pane" value="1" checked class="rounded text-maroon-700 focus:ring-maroon-600">
          Nav Pane
        </label>
        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
          <input type="checkbox" name="show_toolbar" value="1" checked class="rounded text-maroon-700 focus:ring-maroon-600">
          Toolbar
        </label>
        <label class="inline-flex items-center gap-2 text-sm text-slate-700">
          <input type="checkbox" name="allow_client_download" value="1" checked class="rounded text-maroon-700 focus:ring-maroon-600">
          Client Download
        </label>
      </div>
    </div>

    {{-- Granting section --}}
    <div class="p-5 rounded-2xl ring-1 ring-slate-200 grid md:grid-cols-3 gap-6 bg-slate-50/40">
      {{-- User --}}
      <div>
        <h3 class="font-semibold text-slate-800 mb-2">👤 Bagikan ke User</h3>
        <div class="max-h-64 overflow-auto space-y-1">
          @foreach($users as $u)
            <label class="flex items-center gap-2 text-sm text-slate-700">
              <input type="checkbox" name="user_ids[]" value="{{ $u->id }}" class="rounded text-maroon-700 focus:ring-maroon-600">
              {{ $u->name }} <span class="text-slate-500">({{ $u->email }})</span>
            </label>
          @endforeach
        </div>
      </div>

      {{-- Divisi --}}
      <div>
        <h3 class="font-semibold text-slate-800 mb-2">🏢 Bagikan ke Divisi</h3>
        <div class="max-h-64 overflow-auto space-y-1">
          @foreach($divisions as $d)
            <label class="flex items-center gap-2 text-sm text-slate-700">
              <input type="checkbox" name="division_ids[]" value="{{ $d->id }}" class="rounded text-maroon-700 focus:ring-maroon-600">
              {{ $d->name }}
            </label>
          @endforeach
        </div>
      </div>

      {{-- Site --}}
      <div>
        <h3 class="font-semibold text-slate-800 mb-2">📍 Bagikan ke Site</h3>
        <div class="max-h-64 overflow-auto space-y-1">
          @foreach($sites as $s)
            <label class="flex items-center gap-2 text-sm text-slate-700">
              <input type="checkbox" name="site_ids[]" value="{{ $s->id }}" class="rounded text-maroon-700 focus:ring-maroon-600">
              {{ $s->code }} — {{ $s->name }}
            </label>
          @endforeach
        </div>
      </div>
    </div>

    {{-- Tombol --}}
    <div class="flex items-center justify-end gap-3">
      <a href="{{ route('admin.powerbi.index') }}"
         class="px-4 py-2 rounded-xl text-sm font-medium text-slate-700 ring-1 ring-slate-200 hover:bg-slate-50">
        Batal
      </a>
      <button
        class="px-4 py-2 rounded-xl bg-maroon-700 text-white font-semibold hover:bg-maroon-800 ring-1 ring-maroon-900/20">
        Simpan
      </button>
    </div>
  </form>
</div>
@endsection
