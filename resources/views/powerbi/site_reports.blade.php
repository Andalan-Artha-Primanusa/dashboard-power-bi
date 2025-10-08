{{-- resources/views/powerbi/site_reports.blade.php --}}
@extends('layouts.app')
@section('title','Dashboards — '.$site->code)

@section('content')
  {{-- HEADER (Maroon • Putih) --}}
  <div class="mb-6">
    <div class="rounded-2xl overflow-hidden ring-1 ring-slate-200 shadow">
      <div class="relative px-6 py-7 text-white">
        {{-- Base gradient --}}
        <div class="absolute inset-0 bg-gradient-to-r from-maroon-800 via-maroon-700 to-maroon-600"></div>
        {{-- White sheen --}}
        <div class="absolute inset-0 opacity-25 bg-[radial-gradient(70%_70%_at_10%_10%,_rgba(255,255,255,0.5)_0%,_transparent_60%)]"></div>
        {{-- Soft highlight --}}
        <div class="absolute -top-16 -right-16 size-64 rounded-full bg-white/10 blur-3xl"></div>

        <div class="relative">
          <h1 class="text-2xl font-bold tracking-tight">
            📍 {{ $site->code }} — {{ $site->name }}
          </h1>
          <p class="text-white/85 text-sm mt-1">Pilih ARCA</p>
        </div>
      </div>
    </div>
  </div>

  {{-- GRID REPORTS --}}
  <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($reports as $r)
      <a href="{{ route('powerbi.show', $r) }}"
         class="group block rounded-2xl bg-white shadow-md ring-1 ring-slate-200 hover:shadow-lg hover:-translate-y-1 transition p-5">
        <div class="flex items-center justify-between mb-3">
          <div class="h-11 w-11 rounded-xl bg-gradient-to-br from-maroon-700 to-maroon-600 text-white/90 grid place-items-center shadow-inner">
            {{-- chart icon --}}
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-width="2" d="M4 19h16M7 16V8m5 8V5m5 11v-6"/>
            </svg>
          </div>
          <span class="text-[10px] font-mono text-slate-400">#{{ Str::substr($r->id,0,6) }}</span>
        </div>

        <h2 class="text-lg font-semibold text-slate-900 group-hover:text-maroon-700 transition line-clamp-2">
          {{ $r->name }}
        </h2>

        @if(!empty($r->description))
          <p class="mt-1 text-xs text-slate-500 line-clamp-2">{{ $r->description }}</p>
        @endif

        <div class="mt-3 text-[11px] flex items-center gap-2">
          <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-100 text-maroon-900 ring-1 ring-slate-200">
            🔗 Power BI
          </span>
          <span class="text-slate-500">Klik untuk membuka dashboard</span>
        </div>
      </a>
    @empty
      <div class="col-span-full">
        <div class="rounded-xl bg-slate-50 text-slate-800 px-4 py-3 text-sm border border-slate-200">
          ⚠️ Tidak ada report untuk site ini.
        </div>
      </div>
    @endforelse
  </div>

  {{-- PAGINATION --}}
  <div class="mt-6">
    {{ $reports->withQueryString()->links() }}
  </div>
@endsection
