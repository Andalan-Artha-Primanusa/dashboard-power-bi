{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')
@section('title','Dashboard')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-maroon-950 via-maroon-900 to-amber-100/40 py-8">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

    {{-- HERO STRIP (Maroon • Emas) --}}
    <div class="relative overflow-hidden rounded-3xl ring-1 ring-white/10">
      <div class="absolute inset-0 bg-gradient-to-r from-maroon-800 via-maroon-700 to-amber-500"></div>
      <div class="absolute inset-0 opacity-25 bg-[radial-gradient(90%_70%_at_2%_0%,_rgba(255,215,128,0.8),_transparent_60%)]"></div>
      <div class="relative px-6 py-8 sm:px-10 sm:py-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6 text-white">
        <div>
          <div class="text-white/90 text-sm">ARCA — Overview</div>
          <h1 class="mt-1 text-2xl sm:text-3xl font-extrabold tracking-tight">Executive Dashboard</h1>
          <p class="mt-2 text-white/80 text-sm max-w-xl">Snapshot ringkas Power BI, Sites, pengguna, serta distribusi division. Semua dikemas simpel & elegan.</p>
        </div>
        <div class="flex gap-3">
          {{-- Non-klik --}}
          <div role="button" aria-disabled="true"
               class="px-4 py-2 rounded-xl bg-white/85 text-maroon-900 font-semibold shadow-sm pointer-events-none cursor-not-allowed select-none">
            Lihat Semua Dashboards
          </div>
          <div role="button" aria-disabled="true"
               class="px-4 py-2 rounded-xl bg-black/20 text-white font-semibold ring-1 ring-white/20 pointer-events-none cursor-not-allowed select-none">
            Refresh
          </div>
        </div>
      </div>
    </div>

    {{-- KPI CARDS (glass • maroon/emas accents) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="group rounded-2xl bg-white/80 backdrop-blur ring-1 ring-maroon-900/10 p-5 hover:-translate-y-0.5 hover:shadow-lg transition">
        <div class="flex items-center justify-between">
          <div class="text-sm text-slate-600">Power BI Reports</div>
          <div class="h-9 w-9 grid place-items-center rounded-xl bg-amber-100 text-maroon-900 group-hover:bg-amber-200 transition">📊</div>
        </div>
        <div class="mt-1 text-3xl font-extrabold text-slate-900">{{ number_format($totalReports) }}</div>
        <div class="mt-1 text-xs text-slate-500">Total report terdaftar</div>
      </div>

      <div class="group rounded-2xl bg-white/80 backdrop-blur ring-1 ring-maroon-900/10 p-5 hover:-translate-y-0.5 hover:shadow-lg transition">
        <div class="flex items-center justify-between">
          <div class="text-sm text-slate-600">Sites</div>
          <div class="h-9 w-9 grid place-items-center rounded-xl bg-amber-100 text-maroon-900 group-hover:bg-amber-200 transition">🗺️</div>
        </div>
        <div class="mt-1 text-3xl font-extrabold text-slate-900">{{ number_format($totalSites) }}</div>
        <div class="mt-1 text-xs text-slate-500">Site aktif</div>
      </div>

      <div class="group rounded-2xl bg-white/80 backdrop-blur ring-1 ring-maroon-900/10 p-5 hover:-translate-y-0.5 hover:shadow-lg transition">
        <div class="flex items-center justify-between">
          <div class="text-sm text-slate-600">Users</div>
          <div class="h-9 w-9 grid place-items-center rounded-xl bg-amber-100 text-maroon-900 group-hover:bg-amber-200 transition">👥</div>
        </div>
        <div class="mt-1 text-3xl font-extrabold text-slate-900">{{ number_format($totalUsers) }}</div>
        <div class="mt-1 text-xs text-slate-500">Akun terdaftar</div>
      </div>

      <div class="group rounded-2xl bg-white/80 backdrop-blur ring-1 ring-maroon-900/10 p-5 hover:-translate-y-0.5 hover:shadow-lg transition">
        <div class="flex items-center justify-between">
          <div class="text-sm text-slate-600">Divisions</div>
          <div class="h-9 w-9 grid place-items-center rounded-xl bg-amber-100 text-maroon-900 group-hover:bg-amber-200 transition">🏢</div>
        </div>
        <div class="mt-1 text-3xl font-extrabold text-slate-900">{{ number_format($totalDivs) }}</div>
        <div class="mt-1 text-xs text-slate-500">Total division</div>
      </div>
    </div>

    {{-- CHARTS WRAP --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      {{-- Bar: Reports per Site --}}
      <div class="lg:col-span-2 rounded-3xl bg-white/85 backdrop-blur ring-1 ring-maroon-900/10 p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="font-semibold text-slate-800">Reports per Site</h3>
          <span class="text-xs text-slate-500">Total {{ number_format($totalReports) }}</span>
        </div>
        <div class="relative h-[320px]">
          <canvas id="chartReportsPerSite"></canvas>
        </div>
      </div>

      {{-- Doughnut: Users per Division --}}
      <div class="rounded-3xl bg-white/85 backdrop-blur ring-1 ring-maroon-900/10 p-6">
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
    <div class="rounded-3xl bg-white/85 backdrop-blur ring-1 ring-maroon-900/10 p-6">
      <div class="flex items-center justify-between mb-4">
        <div>
          <h3 class="font-semibold text-slate-800">Latest Power BI Reports</h3>
        </div>
        {{-- Non-klik --}}
        <div role="button" aria-disabled="true"
             class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-amber-500/80 text-maroon-900 text-sm font-semibold pointer-events-none cursor-not-allowed select-none ring-1 ring-amber-600/30">
          Lihat semua
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @forelse ($latestReports as $r)
          {{-- Non-klik card --}}
          <div class="group rounded-xl bg-white ring-1 ring-slate-200 hover:ring-amber-300 hover:shadow-lg transition p-4 select-none">
            <div class="flex items-start justify-between">
              <div class="text-xs text-slate-400">#{{ \Illuminate\Support\Str::substr($r->id,0,6) }}</div>
              <span class="text-[10px] px-2 py-0.5 rounded-full bg-amber-50 text-maroon-900 ring-1 ring-amber-200">Power BI</span>
            </div>
            <div class="mt-1 font-semibold text-slate-800 line-clamp-2 group-hover:text-maroon-700 transition">{{ $r->name }}</div>
            <div class="mt-2 text-xs text-slate-500">{{ optional($r->created_at)->format('d M Y') }}</div>
          </div>
        @empty
          <div class="col-span-full rounded-xl bg-amber-50 border border-amber-200 text-maroon-900 p-4">
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

  // Palet maroon • emas
  const MAROON = 'rgba(128, 0, 32, 0.9)';      // maroon-700-ish
  const MAROON_BORDER = 'rgba(86, 0, 22, 1)';   // maroon-800-ish
  const GOLD = 'rgba(245, 197, 66, 0.9)';       // amber-400/500-ish
  const GOLD_BORDER = 'rgba(217, 162, 28, 1)';  // amber-600-ish
  const SLATE = 'rgba(100, 116, 139, 0.3)';

  // Reports per Site (Bar)
  const ctx1 = document.getElementById('chartReportsPerSite').getContext('2d');
  new Chart(ctx1, {
    type: 'bar',
    data: {
      labels: reportsPerSite.map(s => s.code),
      datasets: [{
        label: 'Reports',
        data: reportsPerSite.map(s => s.count),
        backgroundColor: MAROON,
        borderColor: MAROON_BORDER,
        hoverBackgroundColor: GOLD,
        hoverBorderColor: GOLD_BORDER,
        borderWidth: 1,
        borderRadius: 6,
      }]
    },
    options: {
      maintainAspectRatio: false,
      scales: {
        x: {
          ticks: { maxRotation: 0 },
          grid: { display: false }
        },
        y: {
          beginAtZero: true,
          ticks: { precision: 0 },
          grid: { color: SLATE }
        }
      },
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: 'rgba(255,255,255,0.95)',
          titleColor: '#111827',
          bodyColor: '#111827',
          borderColor: 'rgba(0,0,0,0.1)',
          borderWidth: 1
        }
      },
      animation: { duration: 600 }
    }
  });

  // Users per Division (Doughnut)
  const ctx2 = document.getElementById('chartUsersPerDivision').getContext('2d');
  const doughnutColors = [
    MAROON, GOLD, 'rgba(180, 30, 60, 0.9)', 'rgba(255, 214, 102, 0.9)',
    'rgba(90, 10, 25, 0.9)', 'rgba(255, 231, 150, 0.9)'
  ];
  const doughnutBorders = [
    MAROON_BORDER, GOLD_BORDER, 'rgba(120, 15, 40, 1)', 'rgba(217, 162, 28, 1)',
    'rgba(60, 7, 17, 1)', 'rgba(197, 144, 17, 1)'
  ];

  new Chart(ctx2, {
    type: 'doughnut',
    data: {
      labels: usersPerDivision.map(r => r.division),
      datasets: [{
        label: 'Users',
        data: usersPerDivision.map(r => r.count),
        backgroundColor: usersPerDivision.map((_, i) => doughnutColors[i % doughnutColors.length]),
        borderColor: usersPerDivision.map((_, i) => doughnutBorders[i % doughnutBorders.length]),
        borderWidth: 1
      }]
    },
    options: {
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom',
          labels: { boxWidth: 12, boxHeight: 12, padding: 14 }
        },
        tooltip: {
          backgroundColor: 'rgba(255,255,255,0.95)',
          titleColor: '#111827',
          bodyColor: '#111827',
          borderColor: 'rgba(0,0,0,0.1)',
          borderWidth: 1
        }
      },
      cutout: '60%',
      animation: { duration: 600 }
    }
  });
</script>
@endsection
