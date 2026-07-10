{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')
@section('title','Dashboard')

@section('content')
<div class="min-h-screen bg-white py-6">
  {{-- wrapper full-width mengikuti main (tanpa max-w lagi) --}}
  <div class="w-full px-4 sm:px-6 lg:px-8 xl:px-10 space-y-8">

    {{-- HERO STRIP (Maroon • Putih) --}}
    <div class="relative overflow-hidden rounded-3xl ring-1 ring-[#ffffff]/40">
      <div class="absolute inset-0 bg-maroon-800"></div>

      <div class="relative px-6 py-8 sm:px-10 sm:py-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6 text-white">
        <div class="space-y-1">
          <div class="inline-flex items-center gap-2 text-[11px] font-semibold text-white/85 uppercase tracking-wide">
            <span class="inline-flex h-6 w-6 items-center justify-center rounded-xl bg-white/20 ring-1 ring-white/30">
              <img src="{{ asset('assets/images/logoarca.png') }}" alt="ARCA" class="h-4 w-4 object-contain">
            </span>
            <span>ARCA — Executive View</span>
          </div>
          <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
            Executive Dashboard
          </h1>
          <p class="text-white/80 text-sm max-w-xl">
            Snapshot ringkas Power BI, Sites, pengguna, serta distribusi division. Semua dikemas simpel &amp; elegan.
          </p>
        </div>

        <div class="flex flex-wrap gap-3 justify-start md:justify-end">
          <a
            href="{{ route('powerbi.index') }}"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/90 text-maroon-900 font-semibold shadow-sm text-sm hover:bg-white"
          >
            <span class="inline-flex h-5 w-5 items-center justify-center rounded-lg bg-maroon-100 text-maroon-800 text-[11px]">BI</span>
            <span>Buka Flow Dashboard</span>
          </a>
        </div>
      </div>
    </div>

    {{-- KPI CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="group rounded-2xl bg-white/95 backdrop-blur ring-1 ring-maroon-900/10 p-5 hover:-translate-y-0.5 hover:shadow-lg transition">
        <div class="flex items-center justify-between">
          <div class="text-sm text-slate-600">Power BI Reports</div>
          <div class="h-9 w-9 grid place-items-center rounded-xl bg-maroon-50 text-maroon-800 ring-1 ring-maroon-100">
            BI
          </div>
        </div>
        <div class="mt-1 text-3xl font-extrabold text-slate-900">{{ number_format($totalReports) }}</div>
        <div class="mt-1 text-xs text-slate-500">Total report terdaftar</div>
      </div>

      <div class="group rounded-2xl bg-white/95 backdrop-blur ring-1 ring-maroon-900/10 p-5 hover:-translate-y-0.5 hover:shadow-lg transition">
        <div class="flex items-center justify-between">
          <div class="text-sm text-slate-600">Sites</div>
          <div class="h-9 w-9 grid place-items-center rounded-xl bg-maroon-50 text-maroon-800 ring-1 ring-maroon-100">
            ST
          </div>
        </div>
        <div class="mt-1 text-3xl font-extrabold text-slate-900">{{ number_format($totalSites) }}</div>
        <div class="mt-1 text-xs text-slate-500">Site aktif</div>
      </div>

      <div class="group rounded-2xl bg-white/95 backdrop-blur ring-1 ring-maroon-900/10 p-5 hover:-translate-y-0.5 hover:shadow-lg transition">
        <div class="flex items-center justify-between">
          <div class="text-sm text-slate-600">Users</div>
          <div class="h-9 w-9 grid place-items-center rounded-xl bg-maroon-50 text-maroon-800 ring-1 ring-maroon-100">
            US
          </div>
        </div>
        <div class="mt-1 text-3xl font-extrabold text-slate-900">{{ number_format($totalUsers) }}</div>
        <div class="mt-1 text-xs text-slate-500">Akun terdaftar</div>
      </div>

      <div class="group rounded-2xl bg-white/95 backdrop-blur ring-1 ring-maroon-900/10 p-5 hover:-translate-y-0.5 hover:shadow-lg transition">
        <div class="flex items-center justify-between">
          <div class="text-sm text-slate-600">Divisions</div>
          <div class="h-9 w-9 grid place-items-center rounded-xl bg-maroon-50 text-maroon-800 ring-1 ring-maroon-100">
            DV
          </div>
        </div>
        <div class="mt-1 text-3xl font-extrabold text-slate-900">{{ number_format($totalDivs) }}</div>
        <div class="mt-1 text-xs text-slate-500">Total division</div>
      </div>
    </div>

    {{-- CHARTS --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="lg:col-span-2 rounded-3xl bg-white/95 backdrop-blur ring-1 ring-maroon-900/10 p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="font-semibold text-slate-800">Reports per Site</h3>
          <span class="text-xs text-slate-500">Total {{ number_format($totalReports) }}</span>
        </div>
        <div class="relative h-[320px]">
          <canvas id="chartReportsPerSite"></canvas>
        </div>
      </div>

      <div class="rounded-3xl bg-white/95 backdrop-blur ring-1 ring-maroon-900/10 p-6">
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
    <div class="rounded-3xl bg-white/95 backdrop-blur ring-1 ring-maroon-900/10 p-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-slate-800">Latest Power BI Reports</h3>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @forelse ($latestReports as $r)
          <div class="group rounded-xl bg-white ring-1 ring-slate-200 hover:ring-maroon-400 hover:shadow-lg transition p-4 select-none">
            <div class="flex items-start justify-between">
              <div class="text-xs text-slate-400">#{{ \Illuminate\Support\Str::substr($r->id,0,6) }}</div>
              <span class="text-[10px] px-2 py-0.5 rounded-full bg-maroon-50 text-maroon-800 ring-1 ring-maroon-100">
                Power BI
              </span>
            </div>
            <div class="mt-1 font-semibold text-slate-800 line-clamp-2 group-hover:text-maroon-700 transition">
              {{ $r->name }}
            </div>
            <div class="mt-2 text-xs text-slate-500">
              {{ optional($r->created_at)->format('d M Y') }}
            </div>
          </div>
        @empty
          <div class="col-span-full rounded-xl bg-slate-50 border border-slate-200 text-maroon-900 p-4 text-sm">
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
  $reportsPerSiteArr = $reportsPerSite->map(fn($s)=>['code'=>$s->code,'count'=>(int)$s->reports_count])->values();
  $usersPerDivisionArr = $usersPerDivision->map(fn($d)=>['division'=>$d->name ?? 'N/A','count'=>(int)$d->users_count])->values();
@endphp

<script>
  const reportsPerSite = @json($reportsPerSiteArr);
  const usersPerDivision = @json($usersPerDivisionArr);

  // maroon brand (mendekati maroon-700/800)
  const MAROON = '#BD9B75';
  const MAROON_BORDER = '#BD9B75';
  const SLATE = '#BD9B75';

  // Bar chart
  new Chart(document.getElementById('chartReportsPerSite'), {
    type: 'bar',
    data: {
      labels: reportsPerSite.map(s => s.code),
      datasets: [{
        label: 'Reports',
        data: reportsPerSite.map(s => s.count),
        backgroundColor: MAROON,
        borderColor: MAROON_BORDER,
        borderWidth: 1,
        borderRadius: 6
      }]
    },
    options: {
      maintainAspectRatio: false,
      scales: {
        x: { grid: { display: false } },
        y: {
          beginAtZero: true,
          ticks:{ precision:0 },
          grid:{ color: SLATE }
        }
      },
      plugins: { legend:{ display:false } }
    }
  });

  // Doughnut chart
  const doughnutColors = ['#BD9B75'];
  const doughnutBorders = ['#fff'];

  new Chart(document.getElementById('chartUsersPerDivision'), {
    type: 'doughnut',
    data: {
      labels: usersPerDivision.map(r => r.division),
      datasets: [{
        data: usersPerDivision.map(r => r.count),
        backgroundColor: usersPerDivision.map((_,i)=>doughnutColors[i%doughnutColors.length]),
        borderColor: usersPerDivision.map((_,i)=>doughnutBorders[i%doughnutBorders.length]),
        borderWidth:1
      }]
    },
    options: {
      maintainAspectRatio:false,
      plugins:{
        legend:{
          position:'bottom',
          labels:{ boxWidth:12, boxHeight:12, usePointStyle:false }
        }
      },
      cutout:'60%'
    }
  });
</script>
@endsection
