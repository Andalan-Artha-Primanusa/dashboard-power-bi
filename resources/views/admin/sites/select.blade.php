{{-- resources/views/admin/sites/select.blade.php --}}
@extends('layouts.app')

@section('title','Pilih Site Aktif')

@section('content')
<div class="space-y-6">

  {{-- Header --}}
  <div class="rounded-2xl overflow-hidden shadow ring-1 ring-slate-200">
    <div class="px-6 py-6 bg-gradient-to-r from-emerald-600 via-[--teal] to-[--navy]">
      <h1 class="text-2xl font-bold text-white"> Pilih Site Aktif</h1>
      <p class="text-white/80 text-sm mt-1">Semua data & dashboard akan mengikuti site aktif ini.</p>
    </div>
  </div>

  {{-- Alerts --}}
  @if (session('success'))
    <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800">
      {{ session('success') }}
    </div>
  @endif
  @if ($errors->any())
    <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800">
      <ul class="list-disc list-inside">
        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  {{-- Grid Sites --}}
  <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    @forelse($sites as $s)
      <div class="rounded-2xl border shadow-sm p-5 bg-white flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-slate-800">
              {{ $s->code }} — {{ $s->name }}
            </h3>
            @if ($currentSiteId === $s->id)
              <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 ring-1 ring-emerald-200">
                Aktif
              </span>
            @endif
          </div>
          <p class="text-slate-500 text-sm mt-1">{{ $s->region ?: '—' }}</p>
        </div>

        <div class="mt-4 flex items-center gap-2">
          <form action="{{ route('site.switch') }}" method="POST">
            @csrf
            <input type="hidden" name="site_id" value="{{ $s->id }}">
            <button class="px-3 py-2 rounded-xl bg-[--navy] text-white text-sm font-semibold hover:bg-[--navy]/90">
              Gunakan
            </button>
          </form>
          <form action="{{ route('site.setDefault') }}" method="POST">
            @csrf
            <input type="hidden" name="site_id" value="{{ $s->id }}">
            <button class="px-3 py-2 rounded-xl bg-white ring-1 ring-slate-200 text-slate-700 text-sm hover:bg-slate-50">
              Set Default
            </button>
          </form>
        </div>
      </div>
    @empty
      <div class="col-span-full">
        <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-slate-500">
          Belum ada site yang aktif.
        </div>
      </div>
    @endforelse
  </div>

</div>
@endsection
