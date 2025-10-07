{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')
@section('title','Dashboard')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-[#0b1b3f] via-[#0e7a6b] to-[#f4d35e]/30 py-8">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

    {{-- HERO STRIP --}}
    <div class="relative overflow-hidden rounded-3xl ring-1 ring-white/10 bg-gradient-to-r from-[#0b1b3f] via-[#0e7a6b] to-[#f4d35e]">
      <div class="absolute inset-0 opacity-25 bg-[radial-gradient(100%_60%_at_0%_0%,_white,_transparent_60%)]"></div>
      <div class="relative px-6 py-8 sm:px-10 sm:py-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
          <div class="text-white/90 text-sm">BISA ERP — Overview</div>
          <h1 class="mt-1 text-2xl sm:text-3xl font-extrabold tracking-tight text-white">Executive Dashboard</h1>
          <p class="mt-2 text-white/80 text-sm max-w-xl">Snapshot ringkas Power BI, Sites, pengguna, serta distribusi division. Semua dikemas simpel & elegan.</p>
        </div>
        <div class="flex gap-3">
          {{-- Non-klik --}}
          <div role="button" aria-disabled="true"
               class="px-4 py-2 rounded-xl bg-white/80 text-[#0b1b3f] font-semibold shadow-sm pointer-events-none cursor-not-allowed select-none">
            Lihat Semua Dashboards
          </div>
          <div role="button" aria-disabled="true"
               class="px-4 py-2 rounded-xl bg-black/20 text-white font-semibold ring-1 ring-white/20 pointer-events-none cursor-not-allowed select-none">
            Refresh
          </div>
        </div>
      </div>
    </div>

    {{-- KPI CARDS (GLASS) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="group rounded-2xl bg-white/70 backdrop-blur ring-1 ring-black/5 p-5 hover:-translate-y-0.5 hover:shadow-lg transition">
        <div class="flex items-center justify-between">
          <div class="text-sm text-slate-600">Power BI Reports</div>
          <div class="h-9 w-9 grid place-items-center rounded-xl bg-emerald-100 group-hover:bg-emerald-200 transition">📊</div>
        </div>
        <div class="mt-1 text-3xl font-extrabold text-slate-900">{{ number_format($totalReports) }}</div>
        <div class="mt-1 text-xs text-slate-500">Total report terdaftar</div>
      </div>

      <div class="group rounded-2xl bg-white/70 backdrop-blur ring-1 ring-black/5 p-5 hover:-translate-y-0.5 hover:shadow-lg transition">
        <div class="flex items-center justify-between">
          <div class="text-sm text-slate-600">Sites</div>
          <div class="h-9 w-9 grid place-items-center rounded-xl bg-sky-100 group-hover:bg-sky-200 transition">🗺️</div>
        </div>
        <div class="mt-1 text-3xl font-extrabold text-slate-900">{{ number_format($totalSites) }}</div>
        <div class="mt-1 text-xs text-slate-500">Site aktif</div>
      </div>

      <div class="group rounded-2xl bg-white/70 backdrop-blur ring-1 ring-black/5 p-5 hover:-translate-y-0.5 hover:shadow-lg transition">
        <div class="flex items-center justify-between">
          <div class="text-sm text-slate-600">Users</div>
          <div class="h-9 w-9 grid place-items-center rounded-xl bg-amber-100 group-hover:bg-amber-200 transition">👥</div>
        </div>
        <div class="mt-1 text-3xl font-extrabold text-slate-900">{{ number_format($totalUsers) }}</div>
        <div class="mt-1 text-xs text-slate-500">Akun terdaftar</div>
      </div>

      <div class="group rounded-2xl bg-white/70 backdrop-blur ring-1 ring-black/5 p-5 hover:-translate-y-0.5 hover:shadow-lg transition">
        <div class="flex items-center justify-between">
          <div class="text-sm text-slate-600">Divisions</div>
          <div class="h-9 w-9 grid place-items-center rounded-xl bg-violet-100 group-hover:bg-violet-200 transition">🏢</div>
        </div>
        <div class="mt-1 text-3xl font-extrabold text-slate-900">{{ number_format($totalDivs) }}</div>
        <div class="mt-1 text-xs text-slate-500">Total division</div>
      </div>
    </div>

    {{-- CHARTS WRAP --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      {{-- Bar: Reports per Site --}}
      <div class="lg:col-span-2 rounded-3xl bg-white/80 backdrop-blur ring-1 ring-black/5 p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="font-semibold text-slate-800">Reports per Site</h3>
          <span class="text-xs text-slate-500">Total {{ number_format($totalReports) }}</span>
        </div>
        <div class="relative h-[320px]">
          <canvas id="chartReportsPerSite"></canvas>
        </div>
      </div>

      {{-- Doughnut: Users per Division --}}
      <div class="rounded-3xl bg-white/80 backdrop-blur ring-1 ring-black/5 p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="font-semibold text-slate-800">Users per Division</h3>
          <span class="text-xs text-slate-500">Total {{ number_format($totalUsers) }}</span>
        </div>
        <div class="relative h-[320px]">
          <canvas id="chartUsersPerDivision"></canvas>
        </div>
      </div>
    </div>

    {{-- LATEST REPORTS --}}
    <div class="rounded-3xl bg-white/80 backdrop-blur ring-1 ring-black/5 p-6">
      <div class="flex items-center justify-between mb-4">
        <div>
          <h3 class="font-semibold text-slate-800">Latest Power BI Reports</h3>
          <p class="text-xs text-slate-500">8 report terbaru yang ditambahkan</p>
        </div>
        {{-- Non-klik --}}
        <div role="button" aria-disabled="true"
             class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#0e7a6b]/70 text-white text-sm font-semibold pointer-events-none cursor-not-allowed select-none">
          Lihat semua
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @forelse ($latestReports as $r)
          {{-- Non-klik card --}}
          <div class="group rounded-xl bg-white ring-1 ring-slate-200 hover:ring-emerald-300 hover:shadow-lg transition p-4 select-none">
            <div class="flex items-start justify-between">
              <div class="text-xs text-slate-400">#{{ \Illuminate\Support\Str::substr($r->id,0,6) }}</div>
              <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200">Power BI</span>
            </div>
            <div class="mt-1 font-semibold text-slate-800 line-clamp-2 group-hover:text-[#0e7a6b] transition">{{ $r->name }}</div>
            <div class="mt-2 text-xs text-slate-500">{{ optional($r->created_at)->format('d M Y') }}</div>
          </div>
        @empty
          <div class="col-span-full rounded-xl bg-amber-50 border border-amber-200 text-amber-800 p-4">
            Belum ada report.
          </div>
        @endforelse
      </div>
    </div>
  </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@php
  // siapkan array aman untuk JSON
  $reportsPerSiteArr = $reportsPerSite->map(function($s){
    return ['code'=>$s->code,'name'=>$s->name,'count'=>(int) $s->reports_count];
  })->values();

  $usersPerDivisionArr = $usersPerDivision->map(function($d){
    return ['division'=>$d->name ?? 'N/A','count'=>(int) $d->users_count];
  })->values();
@endphp

<script>
  const reportsPerSite = @json($reportsPerSiteArr);
  const usersPerDivision = @json($usersPerDivisionArr);

  // Reports per Site (Bar)
  const ctx1 = document.getElementById('chartReportsPerSite').getContext('2d');
  new Chart(ctx1, {
    type: 'bar',
    data: {
      labels: reportsPerSite.map(s => s.code),
      datasets: [{
        label: 'Reports',
        data: reportsPerSite.map(s => s.count),
        borderWidth: 1
      }]
    },
    options: {
      maintainAspectRatio: false,
      scales: {
        x: { ticks: { maxRotation: 0 } },
        y: { beginAtZero: true, ticks: { precision: 0 } }
      },
      plugins: {
        legend: { display: false },
        tooltip: { enabled: true }
      },
      animation: { duration: 600 }
    }
  });

  // Users per Division (Doughnut)
  const ctx2 = document.getElementById('chartUsersPerDivision').getContext('2d');
  new Chart(ctx2, {
    type: 'doughnut',
    data: {
      labels: usersPerDivision.map(r => r.division),
      datasets: [{
        label: 'Users',
        data: usersPerDivision.map(r => r.count),
        borderWidth: 1
      }]
    },
    options: {
      maintainAspectRatio: false,
      plugins: {
        legend: { position: 'bottom' }
      },
      cutout: '60%',
      animation: { duration: 600 }
    }
  });
</script>
@endsection
