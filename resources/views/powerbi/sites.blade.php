{{-- resources/views/powerbi/sites.blade.php --}}
@extends('layouts.app')
@section('title','Dashboards')

@section('content')
@php
  use Illuminate\Support\Facades\Auth;
  use Illuminate\Support\Str;

  $u = Auth::user();
  $isContextChooser = $isGM ?? false;

  $hasCompanyModel = class_exists(\App\Models\Company::class);
  $CompanyClass = $hasCompanyModel ? \App\Models\Company::class : null;

  $activeCompanyId = session('company_id') ?? ($u?->default_company_id ?? null);
  $activeCompany = null;

  if ($hasCompanyModel && $activeCompanyId) {
    try { $activeCompany = $CompanyClass::find($activeCompanyId); } catch (\Throwable $e) {}
  }

  $companies = collect();
  if ($hasCompanyModel && $isContextChooser) {
    try {
      $companies = $CompanyClass::query()
        ->where('is_active', 1)
        ->orderBy('name')
        ->get(['id','code','name']);
    } catch (\Throwable $e) {}
  }

  $sitesCol = collect($sites ?? []);
@endphp

<div class="space-y-6">
  <section class="rounded-2xl overflow-hidden ring-1 ring-slate-200 shadow bg-white">
    <div class="relative px-6 py-7 text-white">
      <div class="absolute inset-0 bg-maroon-800"></div>

      <div class="relative flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5">
        <div>
          <div class="text-xs uppercase tracking-wide text-white/75">Flow Dashboard</div>
          <h1 class="mt-1 text-2xl font-bold tracking-tight">Pilih context, lalu buka report</h1>
          <p class="mt-2 max-w-2xl text-sm text-white/85">
            Alurnya dibuat satu pintu: pilih perusahaan jika role kamu punya akses, pilih site, lalu pilih dashboard Power BI.
          </p>
        </div>

        <div class="grid grid-cols-3 gap-2 text-center text-xs font-semibold">
          <div class="rounded-xl bg-white/15 ring-1 ring-white/25 px-3 py-2">1. Company</div>
          <div class="rounded-xl bg-white/15 ring-1 ring-white/25 px-3 py-2">2. Site</div>
          <div class="rounded-xl bg-white/15 ring-1 ring-white/25 px-3 py-2">3. Report</div>
        </div>
      </div>
    </div>

    <div class="grid md:grid-cols-2 gap-4 p-5 bg-white">
      <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
        <div class="text-xs uppercase tracking-wide text-slate-500">Perusahaan aktif</div>
        <div class="mt-1 font-semibold text-slate-900">
          {{ $activeCompany ? (($activeCompany->code ?? 'COMP').' - '.$activeCompany->name) : 'Belum dipilih' }}
        </div>
      </div>

      <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
        <div class="text-xs uppercase tracking-wide text-slate-500">Site aktif</div>
        <div class="mt-1 font-semibold text-slate-900">
          {{ $activeSite ? (($activeSite->code ?? 'SITE').' - '.$activeSite->name) : 'Belum dipilih' }}
        </div>
      </div>
    </div>
  </section>

  @if($isContextChooser && $companies->isNotEmpty())
    <section class="space-y-3">
      <div class="flex items-center justify-between gap-4">
        <h2 class="text-sm font-bold text-slate-800">1. Pilih perusahaan</h2>
        <span class="text-xs text-slate-500">{{ $companies->count() }} perusahaan aktif</span>
      </div>

      <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($companies as $c)
          <form action="{{ route('company.switch') }}" method="POST">
            @csrf
            <input type="hidden" name="company_id" value="{{ $c->id }}">
            <button type="submit"
              class="w-full min-h-28 text-left rounded-xl bg-white ring-1 ring-slate-200 hover:ring-maroon-400 hover:shadow-md transition p-4 {{ $activeCompanyId === $c->id ? 'ring-2 ring-maroon-700 bg-maroon-50' : '' }}">
              <div class="flex items-start justify-between gap-3">
                <div>
                  <div class="text-lg font-bold text-slate-900">{{ $c->code ?? 'COMP' }}</div>
                  <div class="mt-1 text-sm text-slate-600 line-clamp-2">{{ $c->name }}</div>
                </div>
                <span class="text-[10px] text-slate-400">#{{ Str::substr($c->id, 0, 6) }}</span>
              </div>
              <div class="mt-3 text-xs {{ $activeCompanyId === $c->id ? 'text-maroon-800 font-semibold' : 'text-slate-500' }}">
                {{ $activeCompanyId === $c->id ? 'Sedang aktif' : 'Aktifkan perusahaan ini' }}
              </div>
            </button>
          </form>
        @endforeach
      </div>
    </section>
  @endif

  @if($isContextChooser && !$activeCompanyId)
    <div class="rounded-xl border border-maroon-200 bg-white px-4 py-3 text-sm text-maroon-800">
      Pilih perusahaan dulu supaya daftar site yang muncul tidak campur.
    </div>
  @else
    <section class="space-y-3">
      <div class="flex items-center justify-between gap-4">
        <h2 class="text-sm font-bold text-slate-800">{{ $isContextChooser ? '2.' : '1.' }} Pilih site</h2>
        <span class="text-xs text-slate-500">{{ $sitesCol->count() }} site tersedia</span>
      </div>

      <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($sitesCol as $s)
          <a href="{{ route('powerbi.site.reports', $s) }}"
             class="group block min-h-32 rounded-xl bg-white ring-1 ring-slate-200 hover:ring-maroon-400 hover:shadow-md transition p-4 {{ $activeSite?->id === $s->id ? 'ring-2 ring-maroon-700 bg-maroon-50' : '' }}">
            <div class="flex items-start justify-between gap-3">
              <div>
                <div class="text-lg font-bold text-slate-900 group-hover:text-maroon-700">{{ $s->code }}</div>
                <div class="mt-1 text-sm text-slate-600 line-clamp-2">{{ $s->name }}</div>
              </div>
              <span class="text-[10px] text-slate-400">#{{ Str::substr($s->id, 0, 6) }}</span>
            </div>

            <div class="mt-4 flex flex-wrap items-center gap-2 text-xs">
              @if(!empty($s->region))
                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-slate-700 ring-1 ring-slate-200">{{ $s->region }}</span>
              @endif
              <span class="rounded-full bg-maroon-50 px-2 py-0.5 text-maroon-800 ring-1 ring-maroon-100">
                Buka report
              </span>
            </div>
          </a>
        @empty
          <div class="col-span-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
            Belum ada site yang bisa dibuka untuk context ini.
          </div>
        @endforelse
      </div>
    </section>
  @endif
</div>
@endsection
