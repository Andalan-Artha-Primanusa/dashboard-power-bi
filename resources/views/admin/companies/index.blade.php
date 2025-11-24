@extends('layouts.app')
@section('title', 'Companies')

@section('content')
@php
  // state filter dari controller
  $q      = $q ?? request('q');
  $active = $active ?? request('active');
  $trash  = $trash ?? request('trash');
@endphp

<div class="max-w-7xl mx-auto space-y-5">

  {{-- Header --}}
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
    <div>
      <h1 class="text-2xl font-bold text-slate-900">Companies</h1>
      <p class="text-sm text-slate-500 mt-1">
        Kelola daftar perusahaan yang ada di ARCA.
      </p>
    </div>

    <a href="{{ route('admin.companies.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-maroon-700 hover:bg-maroon-800 text-white font-semibold text-sm shadow">
      + Create Company
    </a>
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

  {{-- Filters --}}
  <form method="GET" class="rounded-2xl bg-white ring-1 ring-slate-200 p-4 shadow-sm">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
      <div class="md:col-span-5">
        <label class="text-xs font-semibold text-slate-600">Search</label>
        <input type="text" name="q" value="{{ $q }}"
               placeholder="Cari code / nama perusahaan"
               class="mt-1 w-full rounded-xl border-slate-300 focus:border-maroon-600 focus:ring-maroon-600/30">
      </div>

      <div class="md:col-span-3">
        <label class="text-xs font-semibold text-slate-600">Status</label>
        <select name="active"
                class="mt-1 w-full rounded-xl border-slate-300 focus:border-maroon-600 focus:ring-maroon-600/30">
          <option value="">Semua</option>
          <option value="1" @selected((string)$active==='1')>Aktif</option>
          <option value="0" @selected((string)$active==='0')>Nonaktif</option>
        </select>
      </div>

      <div class="md:col-span-2 flex items-center gap-2">
        <label class="inline-flex items-center gap-2 text-sm text-slate-700 mt-6">
          <input type="checkbox" name="trash" value="1" class="rounded border-slate-300"
                 @checked((string)$trash==='1')>
          Trash
        </label>
      </div>

      <div class="md:col-span-2 flex items-center gap-2">
        <button class="w-full md:w-auto px-4 py-2 rounded-xl bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800">
          Apply
        </button>
        <a href="{{ route('admin.companies.index') }}"
           class="w-full md:w-auto px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 text-center">
          Reset
        </a>
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
