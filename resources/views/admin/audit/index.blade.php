{{-- resources/views/admin/audit/index.blade.php --}}
@extends('layouts.app')

@section('title','Audit Log')

@section('content')
@php use Illuminate\Support\Str; @endphp

<div x-data="auditLogIndex()" class="rounded-3xl shadow ring-1 ring-slate-200 bg-white overflow-hidden">

  {{-- HEADER (maroon-only • serumpun) --}}
  <div class="px-6 py-7 text-white relative overflow-hidden">
    {{-- Base gradient --}}
    <div class="absolute inset-0 bg-gradient-to-r from-maroon-800 via-maroon-700 to-maroon-600"></div>
    {{-- White sheen --}}
    <div class="absolute inset-0 opacity-25 bg-[radial-gradient(70%_70%_at_10%_10%,_rgba(255,255,255,0.5)_0%,_transparent_60%)]"></div>
    {{-- Soft overlay --}}
    <div class="absolute -top-16 -right-16 size-64 rounded-full bg-white/10 blur-3xl"></div>

    <div class="relative">
      <h1 class="text-2xl font-bold tracking-tight">ARCA</h1>
      <p class="text-white/85 text-sm mt-1">Semua aktivitas sistem</p>
    </div>
  </div>

  {{-- TOOLBAR (serumpun) --}}
  <div class="px-6 py-4 border-b bg-white">
    <form method="GET" class="grid grid-cols-1 gap-3 sm:grid-cols-12 items-center">
      {{-- Search --}}
      <div class="sm:col-span-6 relative">
        <input type="text" name="q" value="{{ request('q') }}"
               placeholder="Cari aksi, IP, user agent, dll…"
               class="w-full rounded-xl border-slate-300 pl-10 pr-3 py-2.5 text-sm focus:ring-maroon-700 focus:border-maroon-700">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <circle cx="11" cy="11" r="7"/><path d="m21 21-3.5-3.5"/>
        </svg>
      </div>

      {{-- Action filter --}}
      <div class="sm:col-span-3">
        @php $aksi = request('action'); @endphp
        <select name="action"
                class="w-full rounded-xl border-slate-300 px-3 py-2.5 text-sm focus:ring-maroon-700 focus:border-maroon-700">
          <option value="">Semua Aksi</option>
          @foreach($logs->pluck('action')->filter()->unique()->sort() as $act)
            <option value="{{ $act }}" @selected($aksi===$act)>{{ Str::upper($act) }}</option>
          @endforeach
        </select>
      </div>

      {{-- Sort --}}
      <div class="sm:col-span-2">
        @php $sort = request('sort','desc'); @endphp
        <select name="sort"
                class="w-full rounded-xl border-slate-300 px-3 py-2.5 text-sm focus:ring-maroon-700 focus:border-maroon-700">
          <option value="desc" @selected($sort==='desc')>Terbaru dulu</option>
          <option value="asc"  @selected($sort==='asc')>Terlama dulu</option>
        </select>
      </div>

      {{-- Apply --}}
      <div class="sm:col-span-1 sm:justify-self-end">
        <button class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-maroon-700 text-white text-sm font-semibold hover:bg-maroon-800 ring-1 ring-maroon-900/20">
          Terapkan
        </button>
      </div>
    </form>
  </div>

  {{-- LIST / TIMELINE --}}
  <div class="w-full px-4 sm:px-6 py-5">
    <div class="space-y-3">
      @forelse($logs as $log)
        @php
          $ts  = \Illuminate\Support\Carbon::parse($log->created_at);
          $rel = $ts->diffForHumans();

          $raw = is_string($log->payload) ? $log->payload : json_encode($log->payload, JSON_UNESCAPED_SLASHES);
          $arr = json_decode($raw, true);
          $pretty = json_last_error() === JSON_ERROR_NONE
            ? json_encode($arr, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES)
            : ($raw ?? '');

          $ua  = data_get($arr,'user_agent');
          $ip  = data_get($arr,'ip') ?? data_get($log,'ip');

          $action = strtoupper($log->action ?? '-');

          // Badges: maroon/slate for neutral, rose for destructive (serumpun dgn halaman lain)
          $badge  = match(true) {
            str_contains($action,'LOGIN')  => 'bg-maroon-50 text-maroon-900 ring-maroon-200',
            str_contains($action,'LOGOUT') => 'bg-slate-100 text-slate-800 ring-slate-200',
            str_contains($action,'CREATE') => 'bg-maroon-50 text-maroon-900 ring-maroon-200',
            str_contains($action,'UPDATE') => 'bg-maroon-50 text-maroon-900 ring-maroon-200',
            str_contains($action,'DELETE') => 'bg-rose-100 text-rose-800 ring-rose-200',
            default                        => 'bg-slate-100 text-slate-800 ring-slate-200',
          };
        @endphp

        <div x-data="{ open:false }"
             class="rounded-2xl bg-white ring-1 ring-slate-200 shadow-sm hover:shadow-md transition overflow-hidden">
          <div class="grid grid-cols-1 sm:grid-cols-6 lg:grid-cols-12 gap-3 sm:gap-4 items-start px-4 sm:px-5 py-3 sm:py-4">

            {{-- waktu --}}
            <div class="lg:col-span-3 sm:col-span-2">
              <div class="text-sm sm:text-base font-semibold text-slate-900">{{ $rel }}</div>
              <div class="text-xs text-slate-500">{{ $ts->format('Y-m-d H:i:s') }}</div>
            </div>

            {{-- user --}}
            <div class="lg:col-span-3 sm:col-span-2">
              @if($log->causer_id)
                <a href="{{ route('admin.audit.showUser',$log->causer_id) }}"
                   class="text-maroon-700 font-semibold hover:underline break-all">
                  User #{{ $log->causer_id }}
                </a>
              @else
                <span class="text-slate-500">—</span>
              @endif
              @if($ip)
                <div class="text-xs text-slate-500 mt-0.5">IP {{ $ip }}</div>
              @endif
              @if($ua)
                <div class="text-xs text-slate-500 mt-0.5 break-words sm:truncate sm:max-w-[320px]">
                  {{ Str::limit($ua, 110) }}
                </div>
              @endif
            </div>

            {{-- aksi --}}
            <div class="lg:col-span-3 sm:col-span-2">
              <span class="inline-flex items-center text-[11px] sm:text-xs font-semibold px-2 py-0.5 rounded-full ring-1 {{ $badge }}">
                {{ $action }}
              </span>
              @if($log->subject_type || $log->subject_id)
                <div class="text-xs text-slate-500 mt-1 break-words">
                  {{ class_basename($log->subject_type) ?? '-' }} <span class="text-slate-400">#</span>{{ $log->subject_id ?? '-' }}
                </div>
              @endif
            </div>

            {{-- tombol detail (primary maroon, serumpun) --}}
            <div class="lg:col-span-3 sm:col-span-6">
              <div class="sm:text-right">
                <button @click="open=!open"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-maroon-700 text-white text-xs font-semibold hover:bg-maroon-800 ring-1 ring-maroon-900/20">
                  Detail
                </button>
              </div>
            </div>
          </div>

          {{-- panel payload --}}
          <div x-show="open" x-collapse class="px-4 sm:px-5 pb-4 sm:pb-5">
            <div class="rounded-xl border border-slate-200 bg-slate-50 overflow-hidden">
              <div class="px-3 py-2 bg-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div class="text-xs font-semibold text-slate-600">Payload</div>
                <div class="flex items-center gap-2">
                  <button @click="copyText($refs.raw{{ $log->id }})"
                          class="text-xs px-2 py-1 rounded-md bg-white ring-1 ring-slate-300 hover:bg-slate-50">
                    Copy
                  </button>
                </div>
              </div>
              <pre class="p-3 text-[12px] leading-relaxed text-slate-800 overflow-x-auto whitespace-pre">{{ $pretty }}</pre>
              <textarea x-ref="raw{{ $log->id }}" class="sr-only">{{ $raw }}</textarea>
            </div>
          </div>
        </div>
      @empty
        <div class="rounded-2xl border border-slate-200 bg-white p-10 text-center text-slate-500">
          Belum ada log.
        </div>
      @endforelse
    </div>

    <div class="mt-5">
      {{ $logs->appends(request()->query())->links() }}
    </div>
  </div>
</div>

<script>
function auditLogIndex(){
  return {
    copyText(el){
      const v = el?.value ?? el?.textContent ?? '';
      if (!v) return;
      navigator.clipboard.writeText(v);
    }
  }
}
</script>
@endsection
