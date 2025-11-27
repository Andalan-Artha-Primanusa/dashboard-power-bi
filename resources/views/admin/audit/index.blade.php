@extends('layouts.app')
@section('title','Audit — Semua Log')

@section('content')
<div class="space-y-6">

  {{-- HEADER MAROON (seragam ARCA) --}}
  <div class="relative overflow-hidden rounded-3xl ring-1 ring-white/40 bg-gradient-to-r from-maroon-800 via-maroon-700 to-maroon-600">
    {{-- Base gradient / highlight --}}
    <div class="absolute inset-0 opacity-25 bg-[radial-gradient(70%_70%_at_10%_10%,_rgba(255,255,255,0.5)_0%,_transparent_60%)]"></div>
    <div class="absolute -top-16 -right-16 size-64 rounded-full bg-white/10 blur-3xl"></div>

    <div class="relative px-6 py-6 sm:px-8 sm:py-7 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 text-white">
      <div class="space-y-1">
        <div class="inline-flex items-center gap-2 text-[11px] font-semibold text-white/85 uppercase tracking-wide">
          <span class="inline-flex h-6 w-6 items-center justify-center rounded-xl bg-white/20 ring-1 ring-white/30">
            <img src="{{ asset('assets/images/logoarca.png') }}" alt="ARCA" class="h-4 w-4 object-contain">
          </span>
          <span>ARCA — Audit Log</span>
        </div>
        <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight">
          🕵️ Audit Log — Semua User
        </h1>
        <p class="text-xs sm:text-sm text-white/85 mt-1">
          Riwayat aktivitas gabungan. Gunakan pencarian & filter di bawah.
        </p>
      </div>

      <a href="{{ route('admin.audit.export', ['q'=>request('q'),'action'=>request('action')]) }}"
         class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold shadow-sm hover:shadow bg-white text-maroon-900 ring-1 ring-white/20">
        Export CSV
      </a>
    </div>
  </div>

  {{-- CARD UTAMA (FILTER + TABEL) --}}
  <div class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm ring-1 ring-slate-100/80">
    {{-- accent bar kiri --}}
    <div class="absolute inset-y-0 left-0 w-1.5 bg-gradient-to-b from-maroon-700 via-maroon-600 to-maroon-700"></div>

    {{-- FILTER BAR --}}
    <div class="px-6 py-4 sm:px-8 border-b bg-white">
      <form method="GET" class="grid gap-3 sm:grid-cols-12 items-center">
        <label class="sm:col-span-6 relative">
          <input
            type="text"
            name="q"
            value="{{ $q ?? '' }}"
            placeholder="Cari user / action / deskripsi…"
            class="w-full rounded-2xl border border-sky-200 bg-white
                   pl-10 pr-3 py-2.5 text-sm text-slate-700 shadow-sm
                   focus:outline-none focus:ring-2 focus:ring-sky-300/70 focus:border-sky-400" />
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="11" cy="11" r="7"/><path d="m21 21-3.5-3.5"/>
          </svg>
        </label>

        <div class="sm:col-span-4">
          <select
            name="action"
            class="w-full rounded-2xl border border-sky-200 bg-white
                   px-3 py-2.5 text-sm text-slate-700 shadow-sm
                   focus:outline-none focus:ring-2 focus:ring-sky-300/70 focus:border-sky-400">
            <option value="">Semua action</option>
            @foreach($actions as $ac)
              <option value="{{ $ac }}" @selected(($action ?? '')===$ac)>{{ $ac }}</option>
            @endforeach
          </select>
        </div>

        <div class="sm:col-span-2 sm:justify-self-end flex gap-2">
          <button
            class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-maroon-700 text-white text-sm font-medium hover:bg-maroon-800 shadow ring-1 ring-maroon-900/20">
            Terapkan
          </button>
          @if(($q ?? null) || ($action ?? null))
            <a href="{{ route('admin.audit.index') }}"
               class="inline-flex items-center justify-center px-3 py-2 rounded-xl text-xs sm:text-sm font-medium text-slate-700 ring-1 ring-slate-200 bg-white hover:bg-slate-50">
              Reset
            </a>
          @endif
        </div>
      </form>
    </div>

    {{-- TABLE --}}
    <div class="p-6 sm:p-7 overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50 text-slate-600 text-xs font-semibold uppercase border-b">
          <tr>
            <th class="px-4 py-3 text-left">Waktu</th>
            <th class="px-4 py-3 text-left">User</th>
            <th class="px-4 py-3 text-left">Action</th>
            <th class="px-4 py-3 text-left">Subject</th>
            <th class="px-4 py-3 text-left">Payload</th>
            <th class="px-4 py-3 text-left">IP</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
          @forelse ($logs as $l)
            @php
              $actionTxt = strtoupper($l->action ?? '—');
              $badge = match(true){
                str_contains($actionTxt,'DELETE') => 'bg-rose-100 text-rose-700 ring-rose-200',
                str_contains($actionTxt,'LOGIN') || str_contains($actionTxt,'CREATE') || str_contains($actionTxt,'UPDATE')
                                                  => 'bg-maroon-50 text-maroon-900 ring-maroon-200',
                default => 'bg-slate-100 text-slate-700 ring-slate-200',
              };
            @endphp
            <tr class="hover:bg-slate-50 align-top">
              <td class="px-4 py-3 whitespace-nowrap">
                <div class="font-medium text-slate-800">{{ optional($l->created_at)->format('Y-m-d H:i') }}</div>
                <div class="text-xs text-slate-500">{{ optional($l->created_at)->diffForHumans() }}</div>
              </td>
              <td class="px-4 py-3">
                <div class="font-semibold text-slate-900">{{ $l->causer->name ?? '—' }}</div>
                <div class="text-xs text-slate-500">{{ $l->causer->email ?? '' }}</div>
              </td>
              <td class="px-4 py-3">
                <span class="inline-flex items-center text-xs font-semibold px-2 py-0.5 rounded-full ring-1 {{ $badge }}">
                  {{ $actionTxt }}
                </span>
              </td>
              <td class="px-4 py-3">
                <div class="font-medium text-slate-800">{{ class_basename($l->subject_type ?? '—') }}</div>
                <div class="text-xs text-slate-500">ID: {{ $l->subject_id ?? '—' }}</div>
              </td>
              <td class="px-4 py-3">
                <pre class="text-[12px] leading-relaxed bg-slate-50 border border-slate-200 rounded-2xl p-2 max-w-full overflow-x-auto whitespace-pre">
{{ is_string($l->payload ?? null) ? $l->payload : json_encode($l->payload, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}
                </pre>
              </td>
              <td class="px-4 py-3 text-xs text-slate-600">{{ $l->ip ?? '—' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-4 py-12 text-center text-slate-500">Belum ada log ditemukan.</td>
            </tr>
          @endforelse
        </tbody>
      </table>

      <div class="mt-5">
        {{ $logs->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
