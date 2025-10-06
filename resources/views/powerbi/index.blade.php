@extends('layouts.app')
@section('title','Dashboards')
@section('content')
<h1 class="text-xl font-bold mb-4">Dashboards yang kamu bisa akses</h1>
<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
  @forelse($reports as $r)
    <a href="{{ route('powerbi.show',$r) }}" class="p-4 rounded border">
      <div class="text-xs text-slate-500">UUID: {{ $r->id }}</div>
      <div class="font-semibold">{{ $r->name }}</div>
    </a>
  @empty
    <p>Tidak ada akses dashboard.</p>
  @endforelse
</div>
<div class="mt-4">{{ $reports->links() }}</div>
@endsection
