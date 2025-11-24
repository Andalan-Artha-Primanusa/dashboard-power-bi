{{-- resources/views/powerbi/dashboards.blade.php --}}
@extends('layouts.app')
@section('title','Dashboards')

@section('content')
@php
  use Illuminate\Support\Str;
  use Illuminate\Support\Facades\Auth;

  $u = Auth::user();

  // fallback isGM kalau controller gak ngirim
  $isGM = $isGM ?? ($u && method_exists($u,'isGM') ? $u->isGM() : (($u->role ?? '') === 'gm'));
  $isSA = $u && method_exists($u,'isSuperAdmin') ? $u->isSuperAdmin() : (($u->role ?? '') === 'super_admin');
  $isGM = $isGM || $isSA;

  // ====== optional Company model ======
  $hasCompanyModel = false;
  try { $hasCompanyModel = class_exists(\App\Models\Company::class); }
  catch (\Throwable $e) { $hasCompanyModel = false; }
  $CompanyClass = $hasCompanyModel ? \App\Models\Company::class : null;

  // ====== active company ======
  // GM/SA: wajib pilih manual via session (company.switch)
  // Non-GM: lock ke default_company_id (kalau session kosong, auto set)
  if(!$isGM && $u && empty(session('company_id')) && !empty($u->default_company_id)) {
    try { session(['company_id' => $u->default_company_id]); } catch (\Throwable $e) {}
  }

  $activeCompanyId = session('company_id') ?? ($u?->default_company_id ?? null);

  $activeCompany = null;
  if ($hasCompanyModel && $activeCompanyId) {
    try { $activeCompany = $CompanyClass::find($activeCompanyId); } catch (\Throwable $e) {}
  }

  $companyLabel = $activeCompany
    ? (($activeCompany->code ?? 'COMP').' — '.($activeCompany->name ?? ''))
    : 'Belum memilih perusahaan';

  // ====== companies list ======
  $companies = collect();
  if ($hasCompanyModel) {
    try {
      if (!$isGM && $u && method_exists($u,'companies')) {
        $companies = $u->companies()->where('is_active',1)->orderBy('name')->get();
      } else {
        $companies = $CompanyClass::query()->where('is_active',1)->orderBy('name')->get();
      }
    } catch (\Throwable $e) {}
  }

  // ====== sites list ======
  $sitesCol = collect($sites ?? []);

  // filter by company_id kalau kolom ada
  if ($activeCompanyId && $sitesCol->isNotEmpty() && isset($sitesCol->first()->company_id)) {
    $sitesCol = $sitesCol->where('company_id', $activeCompanyId)->values();
  }

  // activeSite fallback (kalau controller gak ngirim)
  $activeSite = $activeSite ?? ($u && method_exists($u,'activeSite') ? $u->activeSite() : null);
@endphp

{{-- HEADER STRIP --}}
<div class="mb-6 px-6 py-7 text-white relative overflow-hidden rounded-3xl shadow ring-1 ring-slate-200">
  <div class="absolute inset-0 bg-gradient-to-r from-maroon-800 via-maroon-700 to-maroon-600"></div>
  <div class="absolute inset-0 opacity-25 bg-[radial-gradient(70%_70%_at_10%_10%,_rgba(255,255,255,0.5)_0%,_transparent_60%)]"></div>
  <div class="absolute -top-16 -right-16 size-64 rounded-full bg-white/10 blur-3xl"></div>

  <div class="relative flex items-start justify-between gap-4">
    <div>
      <h1 class="text-2xl font-bold tracking-tight">
        @if($isGM) 🏭 Pilih Perusahaan @else ARCA @endif
      </h1>
      <p class="text-white/85 text-sm mt-1">
        @if($isGM)
          Urutan akses: <b>Pilih PT dulu</b>, baru pilih Site untuk lihat dashboard.
        @else
          Ringkasan akses Power BI sesuai PT & Site Anda.
        @endif
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

{{-- =========================
     COMPANY SECTION
========================= --}}
@if($companies->isNotEmpty())
  <div class="mb-6">
    @if($isGM)
      <div class="flex items-center justify-between mb-3">
        <div class="text-sm font-bold text-slate-800">🏭 Pilih Perusahaan</div>
        <div class="text-xs text-slate-500">
          Company aktif:
          <b>{{ $activeCompany->code ?? '-' }}</b>
        </div>
      </div>

      <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($companies as $c)
          <form action="{{ route('company.switch') }}" method="POST" class="block">
            @csrf
            <input type="hidden" name="company_id" value="{{ $c->id }}">
            <button type="submit"
              class="w-full text-left group rounded-2xl bg-white shadow-md ring-1 ring-slate-200 hover:shadow-lg hover:-translate-y-0.5 transition p-5
                     {{ $activeCompanyId===$c->id ? 'ring-2 ring-maroon-600/80 bg-maroon-50' : '' }}">
              <div class="flex items-center justify-between mb-2">
                <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-maroon-700 to-maroon-600 text-white/90 grid place-items-center shadow-inner">
                  🏭
                </div>
                <span class="text-[10px] font-mono text-slate-400">#{{ Str::substr($c->id,0,6) }}</span>
              </div>

              <div class="text-lg font-semibold text-slate-900 group-hover:text-maroon-700 transition">
                {{ $c->code ?? 'COMP' }}
              </div>
              <div class="text-sm text-slate-600 line-clamp-2">{{ $c->name }}</div>

              <div class="mt-3 text-xs text-slate-500">
                @if($activeCompanyId===$c->id)
                  ✅ Sedang aktif
                @else
                  Klik untuk aktifkan PT ini
                @endif
              </div>
            </button>
          </form>
        @endforeach
      </div>

      {{-- kalau GM belum pilih PT, stop sampai sini --}}
      @if(!$activeCompanyId)
        <div class="mt-5 rounded-xl bg-slate-50 text-slate-700 px-4 py-3 text-sm border border-slate-200">
          ⚠️ Pilih perusahaan dulu ya, baru daftar site akan muncul.
        </div>
      @endif

    @else
      {{-- Non-GM: tampil PT terkunci --}}
      <div class="rounded-2xl bg-white shadow ring-1 ring-slate-200 p-5">
        <div class="text-xs uppercase tracking-wide text-slate-500">Perusahaan Aktif</div>
        <div class="mt-1 text-lg font-semibold text-slate-900">
          {{ $companyLabel }}
        </div>
        <div class="mt-2 text-sm text-slate-600">
          Perusahaan terkunci sesuai akses akun Anda. Hubungi GM bila perlu perubahan.
        </div>
      </div>
    @endif
  </div>
@endif

{{-- =========================
     SITE SECTION
========================= --}}
@if($isGM)
  {{-- Site muncul hanya kalau PT aktif --}}
  @if($activeCompanyId)

    <div class="flex items-center justify-between mb-3">
      <div class="text-sm font-bold text-slate-800">📍 Pilih Site — {{ $activeCompany->code ?? '' }}</div>
      <div class="text-xs text-slate-500">Total site: {{ $sitesCol->count() }}</div>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
      @forelse($sitesCol as $s)
        <a href="{{ route('powerbi.site.reports', $s) }}"
           class="group block rounded-2xl bg-white shadow-md ring-1 ring-slate-200 hover:shadow-lg hover:-translate-y-1 transition p-5">
          <div class="flex items-center justify-between mb-3">
            <div class="h-11 w-11 rounded-xl bg-gradient-to-br from-maroon-700 to-maroon-600 text-white/90 grid place-items-center shadow-inner">
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
              🏭 {{ $activeCompany->code ?? 'PT' }}
            </span>
          </div>

          <div class="mt-3 text-xs text-slate-500">Klik untuk lihat dashboard site ini</div>
        </a>
      @empty
        <div class="col-span-full">
          <div class="rounded-xl bg-slate-50 text-slate-800 px-4 py-3 text-sm border border-slate-200">
            ⚠️ Belum ada site terdaftar untuk PT ini.
          </div>
        </div>
      @endforelse
    </div>

  @endif

@else
  {{-- Non-GM: tampil list site sesuai PT aktif --}}
  @if($sitesCol->count() > 1)
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
      @foreach($sitesCol as $s)
        <a href="{{ route('powerbi.site.reports', $s) }}"
           class="group block rounded-2xl bg-white shadow-md ring-1 ring-slate-200 hover:shadow-lg hover:-translate-y-1 transition p-5">
          <div class="flex items-center justify-between mb-3">
            <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-maroon-700 to-maroon-600 text-white/90 grid place-items-center shadow-inner">
              📍
            </div>
            <span class="text-[10px] font-mono text-slate-400">#{{ Str::substr($s->id,0,6) }}</span>
          </div>

          <h2 class="text-lg font-semibold text-slate-900 group-hover:text-maroon-700 transition">
            {{ $s->code }}
          </h2>
          <p class="text-sm text-slate-600 line-clamp-2">{{ $s->name }}</p>

          <div class="mt-3 text-xs text-slate-500">Klik untuk buka dashboard</div>
        </a>
      @endforeach
    </div>

  @elseif($activeSite)
    {{-- kalau cuma 1 (default) --}}
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
            @if($activeCompany)
              <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 ring-1 ring-slate-200 text-xs">
                🏭 {{ $activeCompany->code }}
              </span>
            @endif
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 ring-1 ring-slate-200 text-xs">
              🔒 Locked to your account
            </span>
          </div>
          <p class="mt-2 text-sm text-slate-600">
            Anda terkunci pada site ini. Hubungi GM bila perlu perubahan.
          </p>
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
