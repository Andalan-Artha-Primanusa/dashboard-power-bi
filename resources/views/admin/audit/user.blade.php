{{-- resources/views/admin/audit/user.blade.php --}}
@extends('layouts.app')

@section('title', 'Audit Log per User')

@section('content')
<div x-data="auditUserPage()" class="space-y-6">

  {{-- HEADER STRIP --}}
  <div class="rounded-2xl overflow-hidden shadow ring-1 ring-slate-200">
    <div class="px-6 py-6 bg-gradient-to-r from-maroon-700 via-maroon-600 to-yellow-600 text-white">
      <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold tracking-tight">📜 Audit Log</h1>
          <p class="text-white/85 text-sm mt-1">Aktivitas untuk
            <span class="font-semibold">{{ $user->name }}</span>
            <span class="text-white/70">({{ $user->email }})</span>
          </p>
        </div>
        <div class="flex items-center gap-2">
          <a href="{{ route('admin.audit.index') }}"
             class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white/10 text-white ring-1 ring-white/30 hover:bg-white/15">
            ← Kembali
          </a>
        </div>
      </div>
    </div>

    {{-- TOOLBAR --}}
    <div class="px-6 py-4 bg-white">
      <form method="GET" class="grid gap-3 sm:grid-cols-12">
        <div class="sm:col-span-5">
          <label class="sr-only">Cari</label>
          <input type="text" name="q" value="{{ request('q') }}"
                 placeholder="Cari aksi, IP, user agent, dll…"
                 class="w-full rounded-lg border-slate-300 focus:ring-maroon-500 focus:border-maroon-500">
        </div>
        <div class="sm:col-span-3">
          <label class="sr-only">Aksi</label>
          @php $aksi = request('action'); @endphp
          <select name="action"
                  class="w-full rounded-lg border-slate-300 focus:ring-maroon-500 focus:border-maroon-500">
            <option value="">Semua Aksi</option>
            @foreach($logs->pluck('action')->filter()->unique()->sort() as $act)
              <option value="{{ $act }}" @selected($aksi===$act)>{{ Str::upper($act) }}</option>
            @endforeach
          </select>
        </div>
        <div class="sm:col-span-2">
          <label class="sr-only">Urut</label>
          @php $sort = request('sort','desc'); @endphp
          <select name="sort"
                  class="w-full rounded-lg border-slate-300 focus:ring-maroon-500 focus:border-maroon-500">
            <option value="desc" @selected($sort==='desc')>Terbaru dulu</option>
            <option value="asc"  @selected($sort==='asc')>Terlama dulu</option>
          </select>
        </div>
        <div class="sm:col-span-2">
          <button class="w-full inline-flex justify-center items-center gap-2 px-3 py-2 rounded-xl bg-maroon-700 text-white font-semibold hover:bg-maroon-800">
            Terapkan
          </button>
        </div>
      </form>
    </div>
  </div>

  {{-- TABEL --}}
  <div class="rounded-2xl overflow-hidden shadow ring-1 ring-slate-200 bg-white">
    <div class="px-6 py-4 border-b">
      <div class="text-sm text-slate-600">
        Menampilkan <span class="font-semibold">{{ $logs->count() }}</span> dari
        <span class="font-semibold">{{ $logs->total() }}</span> log.
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50 text-slate-600">
          <tr>
            <th class="px-6 py-3 text-left">Waktu</th>
            <th class="px-6 py-3 text-left">Aksi</th>
            <th class="px-6 py-3 text-left">Konteks</th>
            <th class="px-6 py-3 text-left">Detail</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @forelse($logs as $log)
            @php
              $ts      = \Illuminate\Support\Carbon::parse($log->created_at);
              $relative= $ts->diffForHumans();
              $raw     = is_string($log->payload) ? $log->payload : json_encode($log->payload, JSON_UNESCAPED_SLASHES);
              // coba parse json untuk pretty
              $pretty  = $raw;
              try { $pretty = json_encode(json_decode($raw, true), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES); } catch (\Throwable $e) {}
              $ua      = data_get(json_decode($raw, true), 'user_agent') ?? null;
              $ip      = data_get(json_decode($raw, true), 'ip') ?? data_get($log,'ip') ?? null;
              $ctx     = trim(collect([$ip ? "IP $ip" : null, $ua ? \Illuminate\Support\Str::limit($ua, 40) : null])->filter()->implode(' • '));
              $action  = strtoupper($log->action ?? '-');
              $badge   = match(true) {
                str_contains($action,'LOGIN')     => 'bg-emerald-100 text-emerald-800 ring-emerald-200',
                str_contains($action,'LOGOUT')    => 'bg-slate-100 text-slate-800 ring-slate-200',
                str_contains($action,'CREATE')    => 'bg-blue-100 text-blue-800 ring-blue-200',
                str_contains($action,'UPDATE')    => 'bg-amber-100 text-amber-800 ring-amber-200',
                str_contains($action,'DELETE')    => 'bg-rose-100 text-rose-800 ring-rose-200',
                default                            => 'bg-slate-100 text-slate-800 ring-slate-200',
              };
            @endphp
            <tr x-data="{ open:false }" class="hover:bg-slate-50">
              {{-- Waktu --}}
              <td class="align-top px-6 py-3">
                <div class="font-medium text-slate-800" title="{{ $ts->toDayDateTimeString() }}">{{ $relative }}</div>
                <div class="text-xs text-slate-500">{{ $ts->format('Y-m-d H:i:s') }}</div>
              </td>

              {{-- Aksi --}}
              <td class="align-top px-6 py-3">
                <span class="inline-flex items-center text-xs font-semibold px-2 py-0.5 rounded-full ring-1 {{ $badge }}">
                  {{ $action }}
                </span>
                @if($log->subject_type || $log->subject_id)
                  <div class="mt-1 text-xs text-slate-500">
                    {{ class_basename($log->subject_type) ?? '-' }} <span class="text-slate-400">#</span>{{ $log->subject_id ?? '-' }}
                  </div>
                @endif
              </td>

              {{-- Konteks --}}
              <td class="align-top px-6 py-3">
                <div class="text-slate-700">{{ $ctx ?: '—' }}</div>
                @if($site = data_get(json_decode($raw, true), 'site'))
                  <div class="mt-1 text-xs inline-flex items-center px-2 py-0.5 rounded-full bg-maroon-50 text-maroon-800 ring-1 ring-maroon-200">
                    🌐 {{ is_array($site) ? ($site['code'] ?? '-') . ' — ' . ($site['name'] ?? '-') : $site }}
                  </div>
                @endif
              </td>

              {{-- Detail --}}
              <td class="align-top px-6 py-3 w-[560px]">
                <div class="flex items-center gap-2">
                  <button @click="open=!open"
                          class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-slate-900 text-white text-xs font-semibold hover:bg-slate-800">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14M5 12h14" stroke-width="2" stroke-linecap="round"/></svg>
                    {{ __('Lihat Detail') }}
                  </button>
                  <button @click="copyText($refs.raw{{ $log->id }})"
                          class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-white ring-1 ring-slate-300 text-slate-700 text-xs hover:bg-slate-50">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15V5a2 2 0 0 1 2-2h10"/></svg>
                    Copy
                  </button>
                </div>

                {{-- PANEL DETAIL --}}
                <div x-show="open" x-collapse class="mt-3">
                  <div class="rounded-xl border border-slate-200 bg-slate-50 overflow-hidden">
                    <div class="px-3 py-2 bg-slate-100 flex items-center justify-between">
                      <div class="text-xs font-semibold text-slate-600">Payload</div>
                      <div class="flex items-center gap-2">
                        <button @click="toggleFormat($refs.pre{{ $log->id }}, $refs.raw{{ $log->id }})"
                                class="text-xs px-2 py-1 rounded-md bg-white ring-1 ring-slate-300 hover:bg-slate-50">
                          Pretty/Raw
                        </button>
                      </div>
                    </div>
                    <pre x-ref="pre{{ $log->id }}" class="p-3 text-[12px] leading-relaxed text-slate-800 overflow-x-auto whitespace-pre">
{{ $pretty }}
                    </pre>
                    <textarea x-ref="raw{{ $log->id }}" class="sr-only">{{ $raw }}</textarea>
                  </div>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="px-6 py-10">
                <div class="text-center">
                  <div class="mx-auto h-12 w-12 rounded-full bg-slate-100 flex items-center justify-center">
                    <svg class="h-6 w-6 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                  </div>
                  <p class="mt-3 text-slate-600 font-medium">Belum ada log untuk user ini</p>
                  <p class="text-slate-500 text-sm">Aktivitas akan muncul di sini saat user melakukan aksi.</p>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="px-6 py-4 border-t">
      {{ $logs->appends(request()->query())->links() }}
    </div>
  </div>
</div>

{{-- Alpine helpers --}}
<script>
function auditUserPage(){
  return {
    copyText(el){
      const v = el.value ?? el.textContent ?? '';
      navigator.clipboard.writeText(v).then(()=> {
        // kecilkan notifikasi, optional: toast
      });
    },
    toggleFormat(preEl, rawEl){
      try{
        const raw = rawEl.value ?? rawEl.textContent ?? '';
        const isPretty = preEl.dataset.pretty === '1';
        if(isPretty){
          preEl.textContent = raw || '';
          preEl.dataset.pretty = '0';
        } else {
          const obj = JSON.parse(raw || '{}');
          preEl.textContent = JSON.stringify(obj, null, 2);
          preEl.dataset.pretty = '1';
        }
      }catch(e){
        // jika gagal parse, biarkan raw
      }
    }
  }
}
</script>
@endsection
