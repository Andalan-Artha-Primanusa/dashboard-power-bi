{{-- resources/views/powerbi/show.blade.php --}}
@extends('layouts.app')
@section('title', $report->name)

@section('content')
  <h1 class="text-xl font-bold mb-4">{{ $report->name }}</h1>
  <div class="rounded-xl overflow-hidden ring-1 ring-slate-200 bg-white">
    <div class="aspect-video">
      <iframe src="{{ $embedUrl ?? $report->embedUrlWithUI() }}" class="w-full h-full border-0" allowfullscreen></iframe>
    </div>
  </div>
@endsection
