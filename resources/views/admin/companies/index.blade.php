@extends('layouts.app')
@section('title', 'Companies')

@section('content')
@php
  // state filter dari controller
  $q      = $q ?? request('q');
  $active = $active ?? request('active');
  $trash  = $trash ?? request('trash');
@endphp

<div class="w-full space-y-5">

  {{-- HEADER MAROON --}}
  <div class="relative overflow-hidden rounded-3xl ring-1 ring-white/40 bg-gradient-to-r from-maroon-800 via-maroon-700 to-maroon-600">
    {{-- efek glow --}}
    <div class="absolute inset-0 opacity-25 bg-[radial-gradient(90%_70%_at_0%_0%,_rgba(255,255,255,0.55),_transparent_60%)]"></div>

    <div class="relative px-6 py-6 sm:px-8 sm:py-7 flex flex-col md:flex-row md:items-center md:justify-between gap-4 text-white">
      <div class="space-y-1">
        <div class="inline-flex items-center gap-2 text-[11px] font-semibold text-white/85 uppercase tracking-wide">
          <span class="inline-flex h-6 w-6 items-center justify-center rounded-xl bg-white/20 ring-1 ring-white/30">
            <img src="{{ asset('assets/images/logoarca.png') }}" alt="ARCA" class="h-4 w-4 object-contain">
          </span>
          <span>ARCA — Master Data</span>
        </div>
        <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight">
          Companies
        </h1>
        <p class="text-xs sm:text-sm text-white/80 max-w-xl">
          Kelola daftar perusahaan yang ada di ARCA sebagai dasar akses situs, dashboard, dan hak pengguna.
        </p>
      </div>

      <div class="flex flex-wrap gap-3 justify-start md:justify-end">
        <a href="{{ route('admin.companies.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white text-maroon-800 font-semibold text-sm shadow hover:bg-slate-50">
          <span class="inline-flex h-5 w-5 items-center justify-center rounded-lg bg-maroon-100 text-maroon-800 text-[11px]">＋</span>
          <span>Create Company</span>
        </a>
      </div>
    </div>
  </div>

  {{-- Flash --}}
  @if (session('success'))
    <div class="p-3 rounded-xl bg-emerald-50 text-emerald-800 ring-1 ring-emerald-200">
      {{ session('success') }}
    </div>
  @endif
  @if (session('error'))
    <div class="p-3 rounded-xl bg-rose-50 text-rose-800 ring-1 ring-rose-200">
      {{ session('error') }}
    </div>
  @endif

  {{-- FILTERS (serumpun dengan user index) --}}
  <form method="GET"
        class="relative overflow-hidden rounded-2xl border border-slate-300 bg-slate-50/80 shadow-sm">
    {{-- accent bar kiri --}}
    <div class="absolute inset-y-0 left-0 w-1 bg-gradient-to-b from-maroon-700 via-maroon-600 to-maroon-700"></div>

    <div class="px-4 py-4 sm:px-5 sm:py-4">
      {{-- header filter + garis --}}
      <div class="mb-3 flex items-center justify-between">
        <div class="flex items-center gap-2 w-full">
          <span class="text-[11px] font-semibold uppercase tracking-wide text-slate-700">
            Filter Companies
          </span>
          <span class="hidden sm:block flex-1 h-px bg-slate-200"></span>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
        {{-- Search --}}
        <div class="md:col-span-5">
          <label class="text-xs font-semibold text-slate-700">Search</label>
          <div class="mt-1 relative">
            <input type="text" name="q" value="{{ $q }}"
                   placeholder="Cari code / nama perusahaan"
                   class="w-full rounded-xl border border-slate-300 bg-white pl-10 pr-3 py-2.5 text-sm
                          focus:border-maroon-700 focus:ring-2 focus:ring-maroon-700/80">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <circle cx="11" cy="11" r="7"/><path d="m21 21-3.5-3.5"/>
            </svg>
          </div>
        </div>

        {{-- Status --}}
        <div class="md:col-span-3">
          <label class="text-xs font-semibold text-slate-700">Status</label>
          <select name="active"
                  class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm
                         focus:border-maroon-700 focus:ring-2 focus:ring-maroon-700/80">
            <option value="">Semua</option>
            <option value="1" @selected((string)$active==='1')>Aktif</option>
            <option value="0" @selected((string)$active==='0')>Nonaktif</option>
          </select>
        </div>

        {{-- Trash --}}
        <div class="md:col-span-2 flex items-center gap-2">
          <label class="inline-flex items-center gap-2 text-sm text-slate-700 mt-6">
            <input type="checkbox" name="trash" value="1"
                   class="rounded border border-slate-300 text-maroon-700 focus:ring-maroon-700/80"
                   @checked((string)$trash==='1')>
            Trash
          </label>
        </div>

        {{-- Buttons --}}
        <div class="md:col-span-2 flex items-center gap-2 mt-3 md:mt-0">
          <button class="w-full md:w-auto px-4 py-2 rounded-xl bg-maroon-700 text-white text-sm font-semibold hover:bg-maroon-800 ring-1 ring-maroon-900/20">
            Apply
          </button>
          <a href="{{ route('admin.companies.index') }}"
             class="w-full md:w-auto px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 text-center">
            Reset
          </a>
        </div>
      </div>
    </div>
  </form>

  {{-- Table --}}
  <div class="rounded-2xl bg-white ring-1 ring-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50 text-slate-600">
          <tr>
            <th class="px-4 py-3 text-left font-semibold">Code</th>
            <th class="px-4 py-3 text-left font-semibold">Name</th>
            <th class="px-4 py-3 text-left font-semibold">Status</th>
            <th class="px-4 py-3 text-left font-semibold">Updated</th>
            <th class="px-4 py-3 text-right font-semibold">Actions</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-slate-100">
          @forelse($companies as $c)
            <tr class="hover:bg-slate-50/60">
              <td class="px-4 py-3 font-semibold text-slate-900">
                {{ $c->code }}
              </td>
              <td class="px-4 py-3">
                <div class="font-semibold text-slate-900">{{ $c->name }}</div>
                @if($c->description)
                  <div class="text-xs text-slate-500 line-clamp-1">{{ $c->description }}</div>
                @endif
              </td>
              <td class="px-4 py-3">
                @if($c->is_active)
                  <span class="text-xs px-2 py-1 rounded-full bg-emerald-100 text-emerald-800 font-bold">Aktif</span>
                @else
                  <span class="text-xs px-2 py-1 rounded-full bg-slate-200 text-slate-700 font-bold">Nonaktif</span>
                @endif
                @if($c->trashed())
                  <span class="ml-1 text-xs px-2 py-1 rounded-full bg-rose-100 text-rose-800 font-bold">Trash</span>
                @endif
              </td>
              <td class="px-4 py-3 text-slate-600">
                {{ optional($c->updated_at)->format('d M Y H:i') }}
              </td>

              <td class="px-4 py-3">
                <div class="flex items-center justify-end gap-2">
                  @if(!$c->trashed())
                    {{-- Edit --}}
                    <a href="{{ route('admin.companies.edit', $c) }}"
                       class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold">
                      Edit
                    </a>

                    {{-- Toggle active --}}
                    <form action="{{ route('admin.companies.toggle', $c) }}" method="POST">
                      @csrf
                      @method('PATCH')
                      <button type="submit"
                              class="px-3 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-800 text-xs font-bold">
                        {{ $c->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                      </button>
                    </form>

                    {{-- Soft delete --}}
                    <form action="{{ route('admin.companies.destroy', $c) }}" method="POST"
                          onsubmit="return confirm('Hapus perusahaan ini? (soft delete)')">
                      @csrf
                      @method('DELETE')
                      <button type="submit"
                              class="px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold">
                        Delete
                      </button>
                    </form>
                  @else
                    {{-- Restore --}}
                    <form action="{{ route('admin.companies.restore', $c->id) }}" method="POST">
                      @csrf
                      <button type="submit"
                              class="px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-bold">
                        Restore
                      </button>
                    </form>

                    {{-- Force delete --}}
                    <form action="{{ route('admin.companies.forceDelete', $c->id) }}" method="POST"
                          onsubmit="return confirm('Hapus permanen? Tidak bisa dikembalikan!')">
                      @csrf
                      @method('DELETE')
                      <button type="submit"
                              class="px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold">
                        Force Delete
                      </button>
                    </form>
                  @endif

                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                Belum ada perusahaan.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    @if($companies->hasPages())
      <div class="px-4 py-3 border-t border-slate-100">
        {{ $companies->links() }}
      </div>
    @endif
  </div>

</div>
@endsection
