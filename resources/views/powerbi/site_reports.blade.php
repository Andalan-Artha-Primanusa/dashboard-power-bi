{{-- resources/views/powerbi/site_reports.blade.php --}}
@extends('layouts.app')
@section('title','Dashboards — '.$site->code)

@section('content')
  {{-- Header --}}
  <div class="mb-6">
    <div class="rounded-2xl overflow-hidden ring-1 ring-slate-200 shadow">
      <div class="relative px-6 py-6 text-white">
        <div class="absolute inset-0 bg-gradient-to-r from-emerald-600 via-teal-600 to-sky-700"></div>
        <div class="absolute inset-0 opacity-20 bg-[radial-gradient(90%_90%_at_12%_10%,_rgba(255,255,255,.9)_0%,_transparent_60%)]"></div>
        <div class="relative">
          <h1 class="text-2xl font-bold tracking-tight">
            📍 {{ $site->code }} — {{ $site->name }}
          </h1>
          <p class="text-white/85 text-sm mt-1">Pilih dashboard Power BI untuk site ini</p>
        </div>
      </div>
    </div>
  </div>

  {{-- Grid Reports --}}
  <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($reports as $r)
      <a href="{{ route('powerbi.show', $r) }}"
         class="group block rounded-2xl bg-white shadow-md ring-1 ring-slate-200 hover:shadow-lg hover:-translate-y-1 transition p-5">
        <div class="flex items-center justify-between mb-3">
          <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-blue-600 flex items-center justify-center text-white shadow">
            📈
          </div>
          <span class="text-[10px] font-mono text-slate-400">#{{ Str::substr($r->id,0,6) }}</span>
        </div>

        <h2 class="text-lg font-semibold text-slate-800 group-hover:text-indigo-600 transition line-clamp-2">
          {{ $r->name }}
        </h2>

        <div class="mt-3 text-xs text-slate-500">
          Klik untuk membuka dashboard
        </div>
      </a>
    @empty
      <div class="col-span-full">
        <div class="rounded-xl bg-amber-50 text-amber-800 px-4 py-3 text-sm border border-amber-200">
          ⚠️ Tidak ada report untuk site ini.
        </div>
      </div>
    @endforelse
  </div>

  {{-- Pagination --}}
  <div class="mt-6">
    {{ $reports->links() }}
  </div>
@endsection
