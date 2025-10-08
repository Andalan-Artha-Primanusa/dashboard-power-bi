{{-- resources/views/powerbi/dashboards.blade.php --}}
@extends('layouts.app')
@section('title','Dashboards')

@section('content')
  {{-- HEADER STRIP (Maroon • Putih) --}}
  <div class="mb-6 px-6 py-7 text-white relative overflow-hidden rounded-3xl shadow ring-1 ring-slate-200">
    <div class="absolute inset-0 bg-gradient-to-r from-maroon-800 via-maroon-700 to-maroon-600"></div>
    <div class="absolute inset-0 opacity-25 bg-[radial-gradient(70%_70%_at_10%_10%,_rgba(255,255,255,0.5)_0%,_transparent_60%)]"></div>
    <div class="absolute -top-16 -right-16 size-64 rounded-full bg-white/10 blur-3xl"></div>

    <div class="relative flex items-start justify-between gap-4">
      <div>
        <h1 class="text-2xl font-bold tracking-tight">
          @if($isGM) 🌍 Pilih Site @else ARCA @endif
        </h1>
        <p class="text-white/85 text-sm mt-1">
          @if($isGM) Akses dashboard berdasarkan lokasi operasi. @else Ringkasan dan akses cepat laporan Power BI untuk site Anda. @endif
        </p>
      </div>
      @if(!$isGM && $activeSite)
        <a href="{{ route('powerbi.site.reports', $activeSite) }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl font-semibold shadow-sm hover:shadow bg-white text-maroon-900 ring-1 ring-white/20">
          Buka Dashboard
        </a>
      @endif
    </div>
  </div>

  @if($isGM)
    {{-- GRID PILIH SITE --}}
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
      @forelse($sites as $s)
        <a href="{{ route('powerbi.site.reports', $s) }}"
           class="group block rounded-2xl bg-white shadow-md ring-1 ring-slate-200 hover:shadow-lg hover:-translate-y-1 transition p-5">
          <div class="flex items-center justify-between mb-3">
            <div class="h-11 w-11 rounded-xl bg-gradient-to-br from-maroon-700 to-maroon-600 text-white/90 grid place-items-center shadow-inner">
              {{-- pin icon --}}
              <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-width="2" d="M12 21v-7m0 0a5 5 0 1 0-5-5m5 5a5 5 0 0 1 5-5"/>
              </svg>
            </div>
            <span class="text-[10px] font-mono text-slate-400">#{{ Str::substr($s->id,0,6) }}</span>
          </div>

          <h2 class="text-lg font-semibold text-slate-900 group-hover:text-maroon-700 transition">
            {{ $s->code }}
          </h2>
          <p class="text-sm text-slate-600 line-clamp-2">{{ $s->name }}</p>

          <div class="mt-4 flex items-center gap-2 text-[11px]">
            @if($s->region)
              <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-100 text-maroon-900 ring-1 ring-slate-200">
                📍 {{ $s->region }}
              </span>
            @endif
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 ring-1 ring-slate-200">
              🌐 Site
            </span>
          </div>

          <div class="mt-3 text-xs text-slate-500">Klik untuk lihat dashboard site ini</div>
        </a>
      @empty
        <div class="col-span-full">
          <div class="rounded-xl bg-slate-50 text-slate-800 px-4 py-3 text-sm border border-slate-200">
            ⚠️ Belum ada site terdaftar.
          </div>
        </div>
      @endforelse
    </div>

  @else
    {{-- CARD SITE AKTIF / INFO --}}
    @if($activeSite)
      <div class="rounded-2xl bg-white shadow ring-1 ring-slate-200 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div>
            <div class="text-xs uppercase tracking-wide text-slate-500">Site Aktif</div>
            <h2 class="mt-1 text-xl font-semibold text-slate-900">
              {{ $activeSite->code }} — {{ $activeSite->name }}
            </h2>
            <div class="mt-2 flex flex-wrap items-center gap-2">
              @if($activeSite->region)
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-100 text-maroon-900 ring-1 ring-slate-200 text-xs">
                  📍 {{ $activeSite->region }}
                </span>
              @endif
              <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 ring-1 ring-slate-200 text-xs">
                🔒 Locked to your account
              </span>
            </div>
            <p class="mt-2 text-sm text-slate-600">Anda terkunci pada site ini. Hubungi GM bila perlu perubahan.</p>
          </div>

          <a href="{{ route('powerbi.site.reports', $activeSite) }}"
             class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-maroon-700 text-white font-semibold hover:bg-maroon-800 shadow ring-1 ring-maroon-900/20">
            Buka Dashboard
          </a>
        </div>
      </div>
    @else
      <div class="rounded-xl bg-slate-50 text-slate-800 px-4 py-3 text-sm border border-slate-200">
        ⚠️ Anda belum memiliki site default. Hubungi GM untuk menetapkannya.
      </div>
    @endif
  @endif
@endsection
