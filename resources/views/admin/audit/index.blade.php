{{-- resources/views/admin/audit/index.blade.php --}}
@extends('layouts.app')

@section('title','Audit Log')

@section('content')
@php use Illuminate\Support\Str; @endphp

<div x-data="auditLogIndex()" class="space-y-4 sm:space-y-6">

  {{-- HEADER --}}
  <div class="rounded-2xl overflow-hidden shadow ring-1 ring-slate-200">
    <div class="px-3 sm:px-6 py-4 sm:py-6 bg-gradient-to-r from-maroon-700 via-maroon-600 to-yellow-600 text-white">
      <h1 class="text-xl sm:text-2xl font-bold">📜 Audit Log</h1>
      <p class="text-white/85 text-xs sm:text-sm mt-1">Semua aktivitas sistem</p>
    </div>

    {{-- TOOLBAR (responsif) --}}
    <div class="px-3 sm:px-6 py-3 sm:py-4 bg-white">
      <form method="GET" class="grid grid-cols-1 gap-2 sm:grid-cols-12 sm:gap-3">
        {{-- search --}}
        <div class="sm:col-span-6">
          <input type="text" name="q" value="{{ request('q') }}"
                 placeholder="Cari aksi, IP, user agent, dll…"
                 class="w-full rounded-lg border-slate-300 focus:ring-maroon-600 focus:border-maroon-600">
        </div>
        {{-- action filter --}}
        <div class="sm:col-span-3">
          @php $aksi = request('action'); @endphp
          <select name="action"
                  class="w-full rounded-lg border-slate-300 focus:ring-maroon-600 focus:border-maroon-600">
            <option value="">Semua Aksi</option>
            @foreach($logs->pluck('action')->filter()->unique()->sort() as $act)
              <option value="{{ $act }}" @selected($aksi===$act)>{{ Str::upper($act) }}</option>
            @endforeach
          </select>
        </div>
        {{-- sort --}}
        <div class="sm:col-span-2">
          @php $sort = request('sort','desc'); @endphp
          <select name="sort"
                  class="w-full rounded-lg border-slate-300 focus:ring-maroon-600 focus:border-maroon-600">
            <option value="desc" @selected($sort==='desc')>Terbaru dulu</option>
            <option value="asc"  @selected($sort==='asc')>Terlama dulu</option>
          </select>
        </div>
        {{-- apply --}}
        <div class="sm:col-span-1">
          <button class="w-full px-3 py-2 rounded-xl bg-maroon-700 text-white text-sm font-semibold hover:bg-maroon-800">
            Terapkan
          </button>
        </div>
      </form>
    </div>
  </div>

  {{-- LIST / TIMELINE --}}
  <div class="w-full px-2 sm:px-0"> {{-- kecilkan gutter di mobile --}}
    <div class="space-y-2 sm:space-y-3">
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
          $badge  = match(true) {
            str_contains($action,'LOGIN')  => 'bg-emerald-100 text-emerald-800 ring-emerald-200',
            str_contains($action,'LOGOUT') => 'bg-slate-100 text-slate-800 ring-slate-200',
            str_contains($action,'CREATE') => 'bg-blue-100 text-blue-800 ring-blue-200',
            str_contains($action,'UPDATE') => 'bg-amber-100 text-amber-800 ring-amber-200',
            str_contains($action,'DELETE') => 'bg-rose-100 text-rose-800 ring-rose-200',
            default                        => 'bg-slate-100 text-slate-800 ring-slate-200',
          };
        @endphp

        <div x-data="{ open:false }"
             class="rounded-2xl bg-white ring-1 ring-slate-200 shadow-sm hover:shadow-md transition overflow-hidden">
          <div class="grid grid-cols-1 sm:grid-cols-6 lg:grid-cols-12 gap-3 sm:gap-4 items-start px-3 sm:px-5 py-3 sm:py-4">

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

            {{-- tombol detail --}}
            <div class="lg:col-span-3 sm:col-span-6">
              <div class="sm:text-right">
                <button @click="open=!open"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-900 text-white text-xs font-semibold hover:bg-slate-800">
                  Detail
                </button>
              </div>
            </div>
          </div>

          {{-- panel payload --}}
          <div x-show="open" x-collapse class="px-3 sm:px-5 pb-3 sm:pb-5">
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

    <div class="mt-3 sm:mt-4">
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
