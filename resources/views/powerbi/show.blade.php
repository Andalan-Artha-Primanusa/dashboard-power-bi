{{-- resources/views/powerbi/show.blade.php --}}
@extends('layouts.app')
@section('title', $report->name)

@section('content')
  <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
      <div class="text-xs uppercase tracking-wide text-slate-500">Power BI Report</div>
      <h1 class="mt-1 text-xl font-bold text-slate-900">{{ $report->name }}</h1>
    </div>

    <a href="{{ route('powerbi.index') }}"
       class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-maroon-800 ring-1 ring-slate-200 hover:ring-maroon-300">
      Kembali ke flow dashboard
    </a>
  </div>

  <div class="rounded-xl overflow-hidden ring-1 ring-slate-200 bg-white shadow-sm">
    <div class="aspect-video">
      <iframe src="{{ $embedUrl ?? $report->embedUrlWithUI() }}" class="w-full h-full border-0" allowfullscreen></iframe>
    </div>
  </div>
@endsection
