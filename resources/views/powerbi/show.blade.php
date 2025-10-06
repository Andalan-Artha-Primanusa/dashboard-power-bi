@extends('layouts.app')
@section('title', $report->name)
@section('content')
<h1 class="text-xl font-bold mb-3">{{ $report->name }}</h1>
<iframe src="{{ $embedUrl }}" allowfullscreen class="w-full" style="aspect-ratio:16/9;min-height:480px;border:1px solid #e2e8f0"></iframe>
@endsection
