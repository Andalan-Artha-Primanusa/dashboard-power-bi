{{-- resources/views/powerbi/site_reports.blade.php --}}
@extends('layouts.app')
@section('title','Dashboards - '.$site->code)

@section('content')
  <div class="mb-6 rounded-2xl overflow-hidden ring-1 ring-slate-200 shadow bg-white">
    <div class="relative px-6 py-7 text-white">
      <div class="absolute inset-0 bg-maroon-800"></div>

      <div class="relative flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
          <div class="text-xs uppercase tracking-wide text-white/75">Step 3 - Pilih report</div>
          <h1 class="mt-1 text-2xl font-bold tracking-tight">{{ $site->code }} - {{ $site->name }}</h1>
          <p class="mt-2 text-sm text-white/85">Site ini sekarang menjadi site aktif. Pilih dashboard Power BI yang ingin dibuka.</p>
        </div>

        <a href="{{ route('powerbi.index') }}"
           class="inline-flex items-center justify-center rounded-xl bg-white/90 px-4 py-2 text-sm font-semibold text-maroon-900 hover:bg-white">
          Ganti site
        </a>
      </div>
    </div>
  </div>

  <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($reports as $r)
      <a href="{{ route('powerbi.show', $r) }}"
         class="group block min-h-36 rounded-xl bg-white ring-1 ring-slate-200 hover:ring-maroon-400 hover:shadow-md transition p-4">
        <div class="flex items-start justify-between gap-3">
          <div class="h-10 w-10 rounded-xl bg-maroon-700 text-white grid place-items-center font-bold">BI</div>
          <span class="text-[10px] text-slate-400">#{{ Str::substr($r->id, 0, 6) }}</span>
        </div>

        <h2 class="mt-4 text-lg font-semibold text-slate-900 group-hover:text-maroon-700 transition line-clamp-2">
          {{ $r->name }}
        </h2>

        @if(!empty($r->description))
          <p class="mt-1 text-xs text-slate-500 line-clamp-2">{{ $r->description }}</p>
        @endif

        <div class="mt-4 text-xs text-slate-500">Klik untuk membuka dashboard</div>
      </a>
    @empty
      <div class="col-span-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
        Tidak ada report untuk site ini.
      </div>
    @endforelse
  </div>

  <div class="mt-6">
    {{ $reports->withQueryString()->links() }}
  </div>
@endsection
