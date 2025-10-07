{{-- resources/views/powerbi/sites.blade.php --}}
@extends('layouts.app')
@section('title','Dashboards')

@section('content')
  <h1 class="text-2xl font-bold mb-6 text-slate-800">🌍 Pilih Site</h1>

  <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($sites as $s)
      <a href="{{ route('powerbi.site.reports', $s) }}"
         class="group block rounded-2xl bg-white shadow-md ring-1 ring-slate-200 hover:shadow-lg hover:-translate-y-1 transition p-5">
         
        {{-- Icon bulat --}}
        <div class="flex items-center justify-between mb-3">
          <div class="h-10 w-10 rounded-full bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white font-bold shadow">
            🏷️
          </div>
          <span class="text-[10px] font-mono text-slate-400">#{{ Str::substr($s->id,0,6) }}</span>
        </div>

        {{-- Title --}}
        <h2 class="text-lg font-semibold text-slate-800 group-hover:text-emerald-600 transition">
          {{ $s->code }}
        </h2>
        <p class="text-sm text-slate-600">{{ $s->name }}</p>

        {{-- Footer --}}
        <div class="mt-3 text-xs text-slate-500">
          Klik untuk lihat dashboard site ini
        </div>
      </a>
    @empty
      <div class="col-span-full">
        <div class="rounded-xl bg-amber-50 text-amber-800 px-4 py-3 text-sm border border-amber-200">
          ⚠️ Belum ada site terdaftar.
        </div>
      </div>
    @endforelse
  </div>
@endsection
