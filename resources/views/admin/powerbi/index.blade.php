{{-- resources/views/admin/powerbi/index.blade.php --}}
@extends('layouts.app')

@section('title','Power BI Reports')

@section('content')
@php
  $q      = request('q','');
  $status = request('status','all'); // all|active|deleted
@endphp

<div class="rounded-3xl shadow ring-1 ring-slate-200 bg-white overflow-hidden">

  {{-- HEADER STRIP (Maroon • Emas) --}}
  <div class="px-6 py-7 text-white relative overflow-hidden">
    {{-- Base gradient --}}
    <div class="absolute inset-0 bg-gradient-to-r from-maroon-800 via-maroon-700 to-amber-500"></div>
    {{-- Gold sheen --}}
    <div class="absolute inset-0 opacity-25 bg-[radial-gradient(70%_70%_at_10%_10%,_rgba(255,215,128,0.6)_0%,_transparent_60%)]"></div>
    {{-- Soft overlay --}}
    <div class="absolute -top-16 -right-16 size-64 rounded-full bg-white/10 blur-3xl"></div>

    <div class="relative flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div class="text-white">
        <h1 class="text-2xl font-bold tracking-tight">📊 Power BI Reports</h1>
        <p class="text-sm text-white/85 mt-1">Kelola daftar report Power BI dan akses embed-nya.</p>
      </div>
      <a href="{{ route('admin.powerbi.create') }}"
         class="inline-flex items-center gap-2 px-4 py-2 rounded-xl font-semibold shadow-sm hover:shadow bg-amber-400 text-maroon-900 ring-1 ring-white/20">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M11 11V5h2v6h6v2h-6v6h-2v-6H5v-2h6z"/></svg>
        Tambah Report
      </a>
    </div>
  </div>

  {{-- TOOLBAR (match Sites index: maroon/emas) --}}
  <div class="px-6 py-4 border-b bg-white">
    <form method="GET" class="grid gap-3 sm:grid-cols-12 items-center">
      {{-- Search --}}
      <label class="sm:col-span-6 relative">
        <input name="q" value="{{ $q }}" placeholder="Cari nama atau URL…"
               class="w-full rounded-xl border-slate-300 pl-10 pr-3 py-2.5 focus:ring-maroon-700 focus:border-maroon-700" />
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <circle cx="11" cy="11" r="7"/><path d="m21 21-3.5-3.5"/>
        </svg>
      </label>

      {{-- Status filter --}}
      <div class="sm:col-span-4">
        <select name="status"
                class="w-full rounded-xl border-slate-300 px-3 py-2.5 focus:ring-maroon-700 focus:border-maroon-700">
          <option value="all"     {{ $status==='all'?'selected':'' }}>Semua status</option>
          <option value="active"  {{ $status==='active'?'selected':'' }}>Active</option>
          <option value="deleted" {{ $status==='deleted'?'selected':'' }}>Deleted</option>
        </select>
      </div>

      <div class="sm:col-span-2 sm:justify-self-end">
        <button class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-maroon-700 text-white font-medium hover:bg-maroon-800 ring-1 ring-maroon-900/20">
          Terapkan
        </button>
      </div>
    </form>

    {{-- Quick pills (optional, seragam dengan toolbar Sites) --}}
    <div class="mt-3 flex items-center gap-2 text-xs">
      <a href="{{ route('admin.powerbi.index',['status'=>'all','q'=>$q]) }}"
         class="px-3 py-1.5 rounded-lg font-semibold {{ $status==='all' ? 'bg-maroon-700 text-white ring-1 ring-maroon-900 shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
        Semua
      </a>
      <a href="{{ route('admin.powerbi.index',['status'=>'active','q'=>$q]) }}"
         class="px-3 py-1.5 rounded-lg font-semibold {{ $status==='active' ? 'bg-maroon-700 text-white ring-1 ring-maroon-900 shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
        Aktif
      </a>
      <a href="{{ route('admin.powerbi.index',['status'=>'deleted','q'=>$q]) }}"
         class="px-3 py-1.5 rounded-lg font-semibold {{ $status==='deleted' ? 'bg-amber-500 text-maroon-900 ring-1 ring-amber-600 shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
        Terhapus
      </a>
    </div>
  </div>

  {{-- TABLE (desktop) --}}
  <div class="hidden md:block p-6 overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="sticky top-0 z-10 bg-slate-50 text-slate-600 text-xs font-semibold uppercase border-b">
        <tr>
          <th class="px-4 py-3 text-left">Name</th>
          <th class="px-4 py-3 text-left">Embed URL</th>
          <th class="px-4 py-3 text-left">Grants</th>
          <th class="px-4 py-3 text-left">Created By</th>
          <th class="px-4 py-3 text-left">Status</th>
          <th class="px-4 py-3 text-right">Action</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-200">
        @forelse($reports as $report)
          @php
            $isDeleted = method_exists($report,'trashed') ? $report->trashed() : false;
            $uCount = $report->relationLoaded('users') ? $report->users->count() : $report->users()->count();
            $dCount = $report->relationLoaded('divisions') ? $report->divisions->count() : $report->divisions()->count();
            $sCount = $report->relationLoaded('sites') ? $report->sites->count() : $report->sites()->count();
            $isGlobal = ($uCount + $dCount + $sCount) === 0;
          @endphp
          <tr class="hover:bg-slate-50">
            <td class="px-4 py-3 align-top">
              <div class="font-medium text-slate-800">{{ $report->name }}</div>
              @if($report->description)
                <div class="text-xs text-slate-500 line-clamp-1">{{ $report->description }}</div>
              @endif
            </td>
            <td class="px-4 py-3 align-top">
              <a href="{{ $report->embed_url }}" target="_blank"
                 class="text-maroon-700 hover:underline break-all line-clamp-1">Open</a>
            </td>

            {{-- Grants chips (warna diseragamkan) --}}
            <td class="px-4 py-3 align-top">
              <div class="flex flex-wrap gap-2">
                @if($uCount>0)
                  <span class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-full bg-slate-100 text-slate-700 ring-1 ring-slate-200"
                        title="@if($report->relationLoaded('users')){{ $report->users->pluck('name')->join(', ') }}@endif">
                    👤 Users: {{ $uCount }}
                  </span>
                @endif
                @if($dCount>0)
                  <span class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-full bg-amber-100 text-amber-800 ring-1 ring-amber-200"
                        title="@if($report->relationLoaded('divisions')){{ $report->divisions->pluck('name')->join(', ') }}@endif">
                    🏢 Divs: {{ $dCount }}
                  </span>
                @endif
                @if($sCount>0)
                  <span class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-full bg-emerald-100 text-emerald-800 ring-1 ring-emerald-200"
                        title="@if($report->relationLoaded('sites')){{ $report->sites->map(fn($s)=>($s->code ?? '—').($s->name?(' - '.$s->name):''))->join(', ') }}@endif">
                    📍 Sites: {{ $sCount }}
                  </span>
                @endif
                @if($isGlobal)
                  <span class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-full bg-amber-50 text-maroon-900 ring-1 ring-amber-200" title="Tanpa grant: visible global">
                    🌐 Global
                  </span>
                @endif
              </div>
            </td>

            <td class="px-4 py-3 align-top">
              <div class="flex items-center gap-2">
                <div class="h-6 w-6 rounded-full bg-maroon-100 flex items-center justify-center text-[10px] font-bold text-maroon-800 ring-1 ring-maroon-200">
                  {{ strtoupper(substr($report->creator?->name ?? 'U',0,1)) }}
                </div>
                <span class="text-slate-700">{{ $report->creator?->name ?? '-' }}</span>
              </div>
            </td>

            <td class="px-4 py-3 align-top">
              @if($isDeleted)
                <span class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-full bg-rose-100 text-rose-700 ring-1 ring-rose-200">Deleted</span>
              @else
                <span class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-full bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200">Active</span>
              @endif
            </td>

            <td class="px-4 py-3 align-top text-right">
              {{-- Action dropdown (maroon accent) --}}
              <div x-data="{open:false}" class="relative inline-block text-left">
                <button @click="open=!open"
                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold ring-1 ring-slate-200">
                  Actions
                  <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-cloak x-show="open" @click.outside="open=false"
                     class="absolute right-0 z-20 mt-2 w-44 rounded-xl border border-slate-200 bg-white shadow-lg p-1">
                  <a href="{{ route('admin.powerbi.edit',$report) }}" class="block px-3 py-2 rounded-lg hover:bg-slate-50 text-sm">✏️ Edit</a>
                  <a href="{{ $report->embed_url }}" target="_blank" class="block px-3 py-2 rounded-lg hover:bg-slate-50 text-sm text-maroon-700">🔗 Open Embed</a>
                  @if($isDeleted)
                    <form action="{{ route('admin.powerbi.restore',$report->id) }}" method="POST">@csrf
                      <button class="w-full text-left px-3 py-2 rounded-lg text-amber-700 hover:bg-amber-50 text-sm">♻️ Restore</button>
                    </form>
                  @else
                    <form action="{{ route('admin.powerbi.destroy',$report) }}" method="POST" onsubmit="return confirm('Yakin hapus report ini?')">
                      @csrf @method('DELETE')
                      <button class="w-full text-left px-3 py-2 rounded-lg text-rose-700 hover:bg-rose-50 text-sm">🗑️ Delete</button>
                    </form>
                  @endif
                </div>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="px-4 py-16">
              <div class="mx-auto max-w-md text-center">
                <div class="mx-auto h-12 w-12 rounded-2xl bg-slate-100 flex items-center justify-center">
                  <svg class="h-6 w-6 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-slate-800">Belum ada report</h3>
                <p class="mt-1 text-sm text-slate-500">Tambah report Power BI pertama kamu sekarang.</p>
                <a href="{{ route('admin.powerbi.create') }}"
                   class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-xl font-semibold shadow-sm hover:shadow bg-amber-400 text-maroon-900">
                  + Tambah Report
                </a>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>

    <div class="mt-5">
      {{ $reports->appends(['q'=>$q,'status'=>$status])->links() }}
    </div>
  </div>

  {{-- MOBILE CARDS (maroon • emas accents) --}}
  <div class="md:hidden divide-y bg-white">
    @forelse($reports as $report)
      @php
        $isDeleted = method_exists($report,'trashed') ? $report->trashed() : false;
        $uCount = $report->relationLoaded('users') ? $report->users->count() : $report->users()->count();
        $dCount = $report->relationLoaded('divisions') ? $report->divisions->count() : $report->divisions()->count();
        $sCount = $report->relationLoaded('sites') ? $report->sites->count() : $report->sites()->count();
        $isGlobal = ($uCount + $dCount + $sCount) === 0;
      @endphp
      <div class="p-4">
        <div class="flex items-start justify-between gap-2">
          <div class="min-w-0">
            <div class="font-semibold text-slate-900 truncate">{{ $report->name }}</div>
            @if($report->description)
              <div class="text-xs text-slate-500 line-clamp-2">{{ $report->description }}</div>
            @endif

            <div class="mt-1 flex flex-wrap gap-1.5">
              @if($uCount>0)
                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] rounded-full bg-slate-100 text-slate-700 ring-1 ring-slate-200">👤 {{ $uCount }}</span>
              @endif
              @if($dCount>0)
                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] rounded-full bg-amber-100 text-amber-800 ring-1 ring-amber-200">🏢 {{ $dCount }}</span>
              @endif
              @if($sCount>0)
                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] rounded-full bg-emerald-100 text-emerald-800 ring-1 ring-emerald-200">📍 {{ $sCount }}</span>
              @endif
              @if($isGlobal)
                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] rounded-full bg-amber-50 text-maroon-900 ring-1 ring-amber-200">🌐 Global</span>
              @endif
            </div>

            <a href="{{ $report->embed_url }}" target="_blank" class="mt-1 block text-xs text-maroon-700 hover:underline break-all">Open</a>

            <div class="mt-1">
              @if($isDeleted)
                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] rounded-full bg-rose-100 text-rose-700 ring-1 ring-rose-200">Deleted</span>
              @else
                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] rounded-full bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200">Active</span>
              @endif
            </div>
          </div>

          {{-- kebab menu --}}
          <div x-data="{open:false}" class="relative">
            <button @click="open=!open" class="p-2 rounded-lg bg-slate-100 text-slate-700 ring-1 ring-slate-200">⋯</button>
            <div x-cloak x-show="open" @click.outside="open=false"
                 class="absolute right-0 mt-2 w-44 rounded-xl border border-slate-200 bg-white shadow-lg p-1 text-sm">
              <a href="{{ route('admin.powerbi.edit',$report) }}" class="block px-3 py-2 rounded-lg hover:bg-slate-50">✏️ Edit</a>
              <a href="{{ $report->embed_url }}" target="_blank" class="block px-3 py-2 rounded-lg hover:bg-slate-50 text-maroon-700">🔗 Open Embed</a>
              @if($isDeleted)
                <form action="{{ route('admin.powerbi.restore',$report->id) }}" method="POST">@csrf
                  <button class="w-full text-left px-3 py-2 rounded-lg text-amber-700 hover:bg-amber-50">♻️ Restore</button>
                </form>
              @else
                <form action="{{ route('admin.powerbi.destroy',$report) }}" method="POST" onsubmit="return confirm('Yakin hapus report ini?')">
                  @csrf @method('DELETE')
                  <button class="w-full text-left px-3 py-2 rounded-lg text-rose-700 hover:bg-rose-50">🗑️ Delete</button>
                </form>
              @endif
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="p-10 text-center text-slate-600">Belum ada report.</div>
    @endforelse
  </div>
</div>
@endsection
