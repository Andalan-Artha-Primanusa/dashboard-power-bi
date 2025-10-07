{{-- resources/views/admin/sites/index.blade.php --}}
@extends('layouts.app')

@section('title','Daftar Sites')

@section('content')
<div class="rounded-3xl overflow-hidden shadow ring-1 ring-slate-200 bg-white">

  {{-- HEADER STRIP (Maroon • Emas) --}}
  <div class="px-6 py-7 text-white relative overflow-hidden">
    {{-- Base gradient --}}
    <div class="absolute inset-0 bg-gradient-to-r from-maroon-800 via-maroon-700 to-amber-500"></div>
    {{-- Gold sheen --}}
    <div class="absolute inset-0 opacity-25 bg-[radial-gradient(70%_70%_at_10%_10%,_rgba(255,215,128,0.6)_0%,_transparent_60%)]"></div>
    {{-- Soft overlay --}}
    <div class="absolute -top-16 -right-16 size-64 rounded-full bg-white/10 blur-3xl"></div>

    <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold tracking-tight">🏷️ Manajemen Sites</h1>
        <p class="text-white/85 text-sm mt-1">Kelola site, status aktif, dan pemulihan data terhapus.</p>
      </div>
      <a href="{{ route('admin.sites.create') }}"
         class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-400 text-maroon-900 font-semibold hover:bg-amber-300 shadow-sm ring-1 ring-white/20">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Site
      </a>
    </div>
  </div>

  {{-- TOOLBAR --}}
  <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div class="flex items-center gap-2">
      <a href="{{ route('admin.sites.index') }}"
         class="px-3 py-2 rounded-lg text-xs font-semibold transition
                {{ ($only ?? '') !== 'trashed'
                    ? 'bg-maroon-700 text-white ring-1 ring-maroon-900 shadow-sm'
                    : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
        Aktif
      </a>
      <a href="{{ route('admin.sites.index', ['only' => 'trashed']) }}"
         class="px-3 py-2 rounded-lg text-xs font-semibold transition
                {{ ($only ?? '') === 'trashed'
                    ? 'bg-amber-500 text-maroon-900 ring-1 ring-amber-600 shadow-sm'
                    : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
        Terhapus
      </a>
    </div>

    <form method="GET" class="w-full sm:w-auto">
      <div class="flex overflow-hidden rounded-xl ring-1 ring-slate-200 focus-within:ring-2 focus-within:ring-maroon-700 bg-white">
        <span class="px-3 grid place-items-center">
          <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15z"/>
          </svg>
        </span>
        <input type="text" name="q" value="{{ request('q') }}"
               placeholder="Cari kode / nama / region…"
               class="w-full sm:w-72 px-1.5 py-2 text-sm focus:outline-none focus:ring-0"
               autocomplete="off">
        <button class="px-3 py-2 text-sm bg-amber-400 text-maroon-900 font-semibold hover:bg-amber-300">Cari</button>
      </div>
    </form>
  </div>

  {{-- FLASH --}}
  @if (session('success'))
    <div class="px-6 pb-2">
      <div class="text-sm text-emerald-800 bg-emerald-50 px-3 py-2 rounded-lg border border-emerald-200">
        {{ session('success') }}
      </div>
    </div>
  @endif

  {{-- TABLE --}}
  <div class="px-6 pb-6 overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead>
        <tr class="text-left text-slate-600 border-b bg-slate-50">
          <th class="py-2.5 pr-6 font-semibold">Code</th>
          <th class="py-2.5 pr-6 font-semibold">Name</th>
          <th class="py-2.5 pr-6 font-semibold">Region</th>
          <th class="py-2.5 pr-6 font-semibold">Status</th>
          <th class="py-2.5 pr-0 text-right font-semibold">Action</th>
        </tr>
      </thead>
      <tbody class="divide-y">
        @forelse($sites as $site)
          <tr class="hover:bg-slate-50/70 transition">
            <td class="py-3 pr-6 font-mono text-[13px] text-slate-800">{{ $site->code }}</td>
            <td class="py-3 pr-6 font-medium text-slate-900">{{ $site->name }}</td>
            <td class="py-3 pr-6 text-slate-500">{{ $site->region ?: '—' }}</td>
            <td class="py-3 pr-6">
              @if(method_exists($site, 'trashed') && $site->trashed())
                <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 ring-1 ring-slate-200">
                  <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span> Trashed
                </span>
              @else
                @if ($site->is_active)
                  <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 ring-1 ring-emerald-200">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Active
                  </span>
                @else
                  <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 ring-1 ring-amber-200">
                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span> Inactive
                  </span>
                @endif
              @endif
            </td>
            <td class="py-3 pr-0">
              <div class="flex items-center justify-end gap-2">
                @if(method_exists($site, 'trashed') && $site->trashed())
                  <form action="{{ route('admin.sites.restore', $site->id) }}" method="POST">
                    @csrf
                    <button class="px-2.5 py-1.5 text-xs rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 shadow-sm">
                      Restore
                    </button>
                  </form>
                  <form action="{{ route('admin.sites.forceDelete', $site->id) }}" method="POST" onsubmit="return confirm('Hapus permanen?')">
                    @csrf @method('DELETE')
                    <button class="px-2.5 py-1.5 text-xs rounded-lg bg-red-600 text-white hover:bg-red-700 shadow-sm">
                      Force Delete
                    </button>
                  </form>
                @else
                  <form action="{{ route('admin.sites.toggle', $site) }}" method="POST" onsubmit="return confirm('Ubah status aktif/inaktif?')">
                    @csrf @method('PATCH')
                    <button class="px-2.5 py-1.5 text-xs rounded-lg bg-maroon-700 text-white hover:bg-maroon-800 shadow-sm ring-1 ring-maroon-900/30">
                      Toggle
                    </button>
                  </form>
                  <a href="{{ route('admin.sites.edit', $site) }}"
                     class="px-2.5 py-1.5 text-xs rounded-lg bg-amber-400 text-maroon-900 hover:bg-amber-300 shadow-sm ring-1 ring-amber-600/20">
                    Edit
                  </a>
                  <form action="{{ route('admin.sites.destroy', $site) }}" method="POST" onsubmit="return confirm('Pindah ke trash?')">
                    @csrf @method('DELETE')
                    <button class="px-2.5 py-1.5 text-xs rounded-lg bg-red-600 text-white hover:bg-red-700 shadow-sm">
                      Delete
                    </button>
                  </form>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="py-14">
              <div class="flex flex-col items-center gap-2 text-slate-500">
                <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M3 7h18M5 7v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7M9 7V5a3 3 0 0 1 6 0v2"/>
                </svg>
                <div class="text-sm">Belum ada data.</div>
                <a href="{{ route('admin.sites.create') }}"
                   class="mt-2 inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-amber-400 text-maroon-900 text-xs font-semibold hover:bg-amber-300 shadow-sm">
                  + Tambah Site
                </a>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>

    <div class="mt-6">
      {{ $sites->withQueryString()->links() }}
    </div>
  </div>
</div>
@endsection
