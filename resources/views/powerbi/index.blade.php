@extends('layouts.app')
@section('title','Dashboards')

@section('content')
  <h1 class="text-2xl font-bold mb-6 text-slate-800">📊 Dashboards yang bisa kamu akses</h1>

  <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($reports as $r)
      <a href="{{ route('powerbi.show',$r) }}"
         class="group relative block rounded-2xl bg-white shadow-md ring-1 ring-slate-200 hover:shadow-lg hover:-translate-y-1 transition p-5">
         
        {{-- Icon --}}
        <div class="flex items-center justify-between mb-3">
          <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-blue-600 flex items-center justify-center text-white font-bold shadow">
            📈
          </div>
          <span class="text-[10px] font-mono text-slate-400">#{{ Str::substr($r->id,0,6) }}</span>
        </div>

        {{-- Title --}}
        <h2 class="text-lg font-semibold text-slate-800 group-hover:text-indigo-600 transition">
          {{ $r->name }}
        </h2>

        {{-- Footer --}}
        <div class="mt-3 text-xs text-slate-500">
          Klik untuk membuka dashboard
        </div>
      </a>
    @empty
      <div class="col-span-full">
        <div class="rounded-xl bg-amber-50 text-amber-800 px-4 py-3 text-sm border border-amber-200">
          ⚠️ Kamu belum punya akses ke dashboard.
        </div>
      </div>
    @endforelse
  </div>

  <div class="mt-6">
    {{ $reports->links() }}
  </div>
@endsection
