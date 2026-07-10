{{-- resources/views/admin/powerbi/index.blade.php --}}
@extends('layouts.app')

@section('title','Power BI Reports')

@section('content')
@php
  $q      = request('q','');
  $status = request('status','all'); // all|active|deleted
@endphp

<div class="w-full space-y-5">

  {{-- HEADER MAROON (seragam ARCA) --}}
  <div class="relative overflow-hidden rounded-3xl ring-1 ring-white/40 bg-gradient-to-r from-maroon-800 via-maroon-700 to-maroon-600">
    <div class="absolute inset-0 opacity-25 bg-[radial-gradient(90%_70%_at_0%_0%,_rgba(255,255,255,0.55),_transparent_60%)]"></div>
    <div class="absolute -top-16 -right-16 size-64 rounded-full bg-white/10 blur-3xl"></div>

    <div class="relative px-6 py-6 sm:px-8 sm:py-7 flex flex-col md:flex-row md:items-center md:justify-between gap-4 text-white">
      <div class="space-y-1">
        <div class="inline-flex items-center gap-2 text-[11px] font-semibold text-white/85 uppercase tracking-wide">
          <span class="inline-flex h-6 w-6 items-center justify-center rounded-xl bg-white/20 ring-1 ring-white/30">
            <img src="{{ asset('assets/images/logoarca.png') }}" alt="ARCA" class="h-4 w-4 object-contain">
          </span>
          <span>ARCA — Analytics</span>
        </div>
        <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight">
          Power BI Reports
        </h1>
        <p class="text-xs sm:text-sm text-white/80 max-w-xl">
          Kelola daftar report Power BI, embed URL, dan hak aksesnya.
        </p>
      </div>

      <div class="flex flex-wrap gap-3 justify-start md:justify-end">
        <a href="{{ route('admin.powerbi.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl font-semibold shadow bg-white text-maroon-900 hover:bg-slate-50 ring-1 ring-white/20">
          <span class="inline-flex h-5 w-5 items-center justify-center rounded-lg bg-maroon-100 text-maroon-800 text-[11px]">＋</span>
          <span>Tambah Report</span>
        </a>
      </div>
    </div>
  </div>

  {{-- ALERTS (seragam) --}}
  @php
    $statusMsg = session('success') ?? session('status') ?? session('message');
    $errs      = $errors->any() ? $errors->all() : [];
  @endphp
  @if ($statusMsg)
    <div x-data="{open:true}" x-show="open" x-transition
         class="relative rounded-2xl border border-maroon-200 bg-maroon-50/80 ring-1 ring-maroon-100 shadow-sm">
      <div class="flex items-start gap-3 p-4">
        <svg class="h-5 w-5 text-maroon-700 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-width="2" d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="9"/>
        </svg>
        <div class="text-sm text-maroon-900">{{ $statusMsg }}</div>
        <button @click="open=false" class="ml-auto rounded-lg p-1.5 text-maroon-800/80 hover:bg-maroon-100/50">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>
      </div>
      <div class="h-1 w-full bg-gradient-to-r from-maroon-600 via-maroon-500 to-maroon-700"></div>
    </div>
  @elseif (!empty($errs))
    <div x-data="{open:true}" x-show="open" x-transition
         class="relative rounded-2xl border border-rose-200 bg-rose-50/80 ring-1 ring-rose-100 shadow-sm">
      <div class="flex items-start gap-3 p-4">
        <svg class="h-5 w-5 text-rose-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-width="2" d="M12 9v4m0 4h.01"/><path stroke-width="2" d="M12 2a10 10 0 100 20 10 10 0 000-20z"/>
        </svg>
        <div class="text-sm text-rose-900">
          <div class="font-semibold">Terjadi kesalahan:</div>
          <ul class="list-disc ml-5 mt-1 space-y-0.5">
            @foreach ($errs as $err) <li>{{ $err }}</li> @endforeach
          </ul>
        </div>
        <button @click="open=false" class="ml-auto rounded-lg p-1.5 text-rose-700/80 hover:bg-rose-100">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </button>
      </div>
      <div class="h-1 w-full bg-gradient-to-r from-rose-500 via-rose-400 to-rose-600"></div>
    </div>
  @endif

  {{-- FILTER CARD (diseragamkan + accent bar kiri) --}}
  <form method="GET"
        class="relative overflow-hidden rounded-2xl border border-slate-300 bg-slate-50/80 shadow-sm">
    {{-- accent bar kiri --}}
    <div class="absolute inset-y-0 left-0 w-1 bg-gradient-to-b from-maroon-700 via-maroon-600 to-maroon-700"></div>

    <div class="px-4 py-4 sm:px-5 sm:py-4">
      {{-- header filter --}}
      <div class="mb-3 flex items-center justify-between">
        <div class="flex items-center gap-2 w-full">
          <span class="text-[11px] font-semibold uppercase tracking-wide text-slate-700">
            Filter Power BI Reports
          </span>
          <span class="hidden sm:block flex-1 h-px bg-slate-200"></span>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
        {{-- Search --}}
        <div class="md:col-span-6">
          <label class="text-xs font-semibold text-slate-700">Search</label>
          <div class="mt-1 relative">
            <input name="q" value="{{ $q }}" placeholder="Cari nama atau URL…"
                   class="w-full rounded-xl border border-slate-300 bg-white pl-10 pr-3 py-2.5 text-sm
                          focus:border-maroon-700 focus:ring-2 focus:ring-maroon-700/80" />
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <circle cx="11" cy="11" r="7"/><path d="m21 21-3.5-3.5"/>
            </svg>
          </div>
        </div>

        {{-- Status --}}
        <div class="md:col-span-4">
          <label class="text-xs font-semibold text-slate-700">Status</label>
          <select name="status"
                  class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm
                         focus:border-maroon-700 focus:ring-2 focus:ring-maroon-700/80">
            <option value="all"     {{ $status==='all'?'selected':'' }}>Semua status</option>
            <option value="active"  {{ $status==='active'?'selected':'' }}>Active</option>
            <option value="deleted" {{ $status==='deleted'?'selected':'' }}>Deleted</option>
          </select>
        </div>

        {{-- Tombol --}}
        <div class="md:col-span-2 flex items-center gap-2 mt-3 md:mt-0">
          <button class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-maroon-700 text-white text-sm font-semibold hover:bg-maroon-800 ring-1 ring-maroon-900/20">
            Terapkan
          </button>
          <a href="{{ route('admin.powerbi.index') }}"
             class="w-full md:w-auto px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 text-center">
            Reset
          </a>
        </div>
      </div>

      {{-- Quick pills --}}
      <div class="mt-3 flex flex-wrap items-center gap-2 text-xs">
        <a href="{{ route('admin.powerbi.index',['status'=>'all','q'=>$q]) }}"
           class="px-3 py-1.5 rounded-lg font-semibold {{ $status==='all' ? 'bg-maroon-700 text-white ring-1 ring-maroon-900 shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
          Semua
        </a>
        <a href="{{ route('admin.powerbi.index',['status'=>'active','q'=>$q]) }}"
           class="px-3 py-1.5 rounded-lg font-semibold {{ $status==='active' ? 'bg-maroon-700 text-white ring-1 ring-maroon-900 shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
          Aktif
        </a>
        <a href="{{ route('admin.powerbi.index',['status'=>'deleted','q'=>$q]) }}"
           class="px-3 py-1.5 rounded-lg font-semibold {{ $status==='deleted' ? 'bg-maroon-700 text-white ring-1 ring-maroon-900 shadow-sm' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
          Terhapus
        </a>
      </div>
    </div>
  </form>

  {{-- CARD DATA (table + mobile) --}}
  <div class="rounded-2xl shadow ring-1 ring-slate-200 bg-white overflow-hidden">

    {{-- TABLE (desktop) --}}
    <div class="hidden md:block p-6 overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50 text-slate-600 text-xs font-semibold uppercase border-b">
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
          @forelse($reports as $r)
            @php
              $isDeleted = method_exists($r,'trashed') ? $r->trashed() : false;
              $uCount = $r->relationLoaded('users') ? $r->users->count() : $r->users()->count();
              $dCount = $r->relationLoaded('divisions') ? $r->divisions->count() : $r->divisions()->count();
              $sCount = $r->relationLoaded('sites') ? $r->sites->count() : $r->sites()->count();
              $isGlobal = ($uCount + $dCount + $sCount) === 0;
            @endphp
            <tr class="{{ $isDeleted ? 'bg-rose-50/40' : 'hover:bg-slate-50' }}"
                x-data="{ open:false, confirmDelete:false }">
              <td class="px-4 py-3 align-top">
                <div class="font-medium text-slate-800">{{ $r->name }}</div>
                @if($r->description)
                  <div class="text-xs text-slate-500 line-clamp-1">{{ $r->description }}</div>
                @endif
              </td>

              <td class="px-4 py-3 align-top">
                <a href="{{ $r->embed_url }}" target="_blank"
                   class="text-maroon-700 hover:underline break-all line-clamp-1">Open</a>
              </td>

              {{-- Grants chips --}}
              <td class="px-4 py-3 align-top">
                <div class="flex flex-wrap gap-2">
                  @if($uCount>0)
                    <span class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-full bg-slate-100 text-slate-700 ring-1 ring-slate-200"> Users: {{ $uCount }}</span>
                  @endif
                  @if($dCount>0)
                    <span class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-full bg-slate-100 text-slate-700 ring-1 ring-slate-200"> Divs: {{ $dCount }}</span>
                  @endif
                  @if($sCount>0)
                    <span class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-full bg-slate-100 text-slate-700 ring-1 ring-slate-200"> Sites: {{ $sCount }}</span>
                  @endif
                  @if($isGlobal)
                    <span class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-full bg-slate-100 text-maroon-900 ring-1 ring-slate-200"> Global</span>
                  @endif
                </div>
              </td>

              <td class="px-4 py-3 align-top">
                <div class="flex items-center gap-2">
                  <div class="h-6 w-6 rounded-full bg-maroon-100 flex items-center justify-center text-[10px] font-bold text-maroon-800 ring-1 ring-maroon-200">
                    {{ strtoupper(substr($r->creator?->name ?? 'U',0,1)) }}
                  </div>
                  <span class="text-slate-700">{{ $r->creator?->name ?? '-' }}</span>
                </div>
              </td>

              <td class="px-4 py-3 align-top">
                @if($isDeleted)
                  <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-rose-100 text-rose-700 ring-1 ring-rose-200 text-xs font-medium">Deleted</span>
                @else
                  <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-maroon-50 text-maroon-900 ring-1 ring-maroon-200 text-xs font-medium">Active</span>
                @endif
              </td>

              <td class="px-4 py-3 align-top text-right">
                {{-- ACTIONS dropdown --}}
                <div class="relative inline-block text-left">
                  <button @click="open=!open"
                          class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">
                    Actions
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                  </button>

                  <div x-cloak x-show="open" @click.outside="open=false"
                       class="absolute right-0 z-20 mt-2 w-48 rounded-xl border border-slate-200 bg-white shadow-lg py-1 text-sm">
                    @if($isDeleted)
                      <form method="POST" action="{{ route('admin.powerbi.restore',$r->id) }}">
                        @csrf
                        <button class="w-full text-left px-3 py-2 hover:bg-slate-50 text-maroon-700"> Restore</button>
                      </form>
                      <button type="button" @click="open=false; confirmDelete=true"
                              class="w-full text-left px-3 py-2 hover:bg-slate-50 text-rose-600"> Hapus Permanen</button>
                    @else
                      <a href="{{ route('admin.powerbi.edit',$r) }}" class="block px-3 py-2 hover:bg-slate-50"> Edit</a>
                      <a href="{{ $r->embed_url }}" target="_blank" class="block px-3 py-2 hover:bg-slate-50 text-maroon-700"> Open Embed</a>
                      <button type="button" @click="open=false; confirmDelete=true"
                              class="w-full text-left px-3 py-2 hover:bg-slate-50 text-rose-600"> Delete</button>
                    @endif
                  </div>
                </div>

                {{-- Hidden forms (soft & hard delete) --}}
                <form x-ref="deleteFormSoft" class="hidden" method="POST" action="{{ route('admin.powerbi.destroy',$r) }}">
                  @csrf @method('DELETE')
                </form>
              

                {{-- DELETE MODAL --}}
                <div x-cloak x-show="confirmDelete" x-transition.opacity.duration.200ms class="fixed inset-0 z-40"
                     role="dialog" aria-modal="true" aria-labelledby="delTitle-{{ $r->id }}"
                     @keydown.escape.window="confirmDelete=false">
                  <div class="absolute inset-0 bg-black/40 backdrop-blur-[2px]" @click="confirmDelete=false" x-transition.opacity.duration.200ms></div>
                  <div class="absolute inset-0 flex items-center justify-center p-4">
                    <div x-show="confirmDelete"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2 scale-[0.98]"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-2 scale-[0.98]"
                         class="w-full max-w-md rounded-2xl bg-white/95 backdrop-blur-sm shadow-2xl ring-1 ring-slate-200/80 overflow-hidden"
                         x-data="{ ack:false, text:'' }"
                         x-init="$nextTick(()=> $el.querySelector('[data-cancel]')?.focus())">

                      <div class="px-5 pt-5 pb-3 flex items-start gap-3">
                        <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-rose-600 to-rose-700 text-white flex items-center justify-center shadow-inner">
                          <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path d="M3 6h18" stroke-width="2"/>
                            <path d="M8 6v-1a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v1" stroke-width="2"/>
                            <rect x="5" y="6" width="14" height="14" rx="2" stroke-width="2"/>
                            <path d="M10 11v6M14 11v6" stroke-width="2"/>
                          </svg>
                        </div>
                        <div class="min-w-0">
                          <h3 id="delTitle-{{ $r->id }}" class="text-base font-semibold text-slate-900">
                            @if($isDeleted) Hapus Permanen Report @else Hapus Report @endif
                          </h3>
                          <p class="mt-0.5 text-sm text-slate-500">Tindakan ini tidak dapat dibatalkan.</p>
                        </div>
                        <button data-cancel @click="confirmDelete=false" class="ml-auto rounded-lg p-1.5 text-slate-500 hover:bg-slate-100/70">
                          <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M18 6 6 18M6 6l12 12"/></svg>
                        </button>
                      </div>

                      <div class="px-5 pb-4 space-y-3">
                        <div class="rounded-xl ring-1 ring-slate-200 bg-slate-50/60 px-3 py-2 text-sm">
                          Hapus <span class="font-semibold text-slate-800">{{ $r->name }}</span>?
                          @if($isDeleted) <span class="text-rose-600 font-semibold">(PERMANEN)</span> @endif
                        </div>
                        <label class="flex items-start gap-2 text-sm text-slate-700">
                          <input type="checkbox" class="mt-0.5 rounded border-slate-300 text-rose-600 focus:ring-rose-500" x-model="ack">
                          <span>Saya memahami konsekuensi penghapusan ini.</span>
                        </label>
                        <div>
                          <label class="block text-[13px] text-slate-500 mb-1">Ketik <span class="font-semibold text-slate-700">HAPUS</span> untuk konfirmasi</label>
                          <input type="text" x-model.trim="text" placeholder="HAPUS"
                                 class="w-full rounded-xl border-slate-300 focus:border-rose-500 focus:ring-rose-500 text-sm"/>
                        </div>
                      </div>

                      <div class="h-px bg-gradient-to-r from-transparent via-slate-200 to-transparent"></div>

                      <div class="px-5 py-4 flex items-center justify-end gap-2.5">
                        <button data-cancel @click="confirmDelete=false"
                                class="px-3.5 py-2 rounded-xl text-slate-700 ring-1 ring-slate-200 hover:bg-slate-50 text-sm font-medium">
                          Batal
                        </button>
                        <button :class="(ack && text==='HAPUS') ? 'opacity-100' : 'opacity-50 cursor-not-allowed'"
                                :disabled="!(ack && text==='HAPUS')"
                                @click="( {{ $isDeleted ? '$refs.deleteFormHard' : '$refs.deleteFormSoft' }} ).submit()"
                                class="px-3.5 py-2 rounded-xl bg-gradient-to-r from-rose-600 to-rose-700 text-white text-sm font-semibold shadow hover:brightness-[1.03] active:scale-[0.99]">
                          Ya, Hapus
                        </button>
                      </div>

                    </div>
                  </div>
                </div>
                {{-- /DELETE MODAL --}}
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
                     class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-xl font-semibold shadow bg-maroon-700 text-white hover:bg-maroon-800">
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

    {{-- MOBILE CARDS --}}
    <div class="md:hidden divide-y bg-white">
      @forelse($reports as $r)
        @php
          $isDeleted = method_exists($r,'trashed') ? $r->trashed() : false;
          $uCount = $r->relationLoaded('users') ? $r->users->count() : $r->users()->count();
          $dCount = $r->relationLoaded('divisions') ? $r->divisions->count() : $r->divisions()->count();
          $sCount = $r->relationLoaded('sites') ? $r->sites->count() : $r->sites()->count();
          $isGlobal = ($uCount + $dCount + $sCount) === 0;
        @endphp
        <div class="p-4" x-data="{ open:false, confirmDelete:false }">
          <div class="flex items-start justify-between gap-2">
            <div class="min-w-0">
              <div class="font-semibold text-slate-900 truncate">{{ $r->name }}</div>
              @if($r->description)
                <div class="text-xs text-slate-500 line-clamp-2">{{ $r->description }}</div>
              @endif

              <div class="mt-1 flex flex-wrap gap-1.5">
                @if($uCount>0)
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] rounded-full bg-slate-100 text-slate-700 ring-1 ring-slate-200"> {{ $uCount }}</span>
                @endif
                @if($dCount>0)
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] rounded-full bg-slate-100 text-slate-700 ring-1 ring-slate-200"> {{ $dCount }}</span>
                @endif
                @if($sCount>0)
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] rounded-full bg-slate-100 text-slate-700 ring-1 ring-slate-200"> {{ $sCount }}</span>
                @endif
                @if($isGlobal)
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] rounded-full bg-slate-100 text-maroon-900 ring-1 ring-slate-200"> Global</span>
                @endif
              </div>

              <a href="{{ $r->embed_url }}" target="_blank" class="mt-1 block text-xs text-maroon-700 hover:underline break-all">Open</a>

              <div class="mt-1">
                @if($isDeleted)
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] rounded-full bg-rose-100 text-rose-700 ring-1 ring-rose-200">Deleted</span>
                @else
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] rounded-full bg-maroon-50 text-maroon-900 ring-1 ring-maroon-200">Active</span>
                @endif
              </div>
            </div>

            <div class="relative">
              <button @click="open=!open" class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 text-xs font-semibold ring-1 ring-slate-200">
                Actions
                <svg class="inline h-3.5 w-3.5 ml-1 align-[-2px]" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
              </button>
              <div x-cloak x-show="open" @click.outside="open=false"
                   class="absolute right-0 mt-2 w-48 rounded-xl border border-slate-200 bg-white shadow-lg p-1 text-sm">
                @if($isDeleted)
                  <form method="POST" action="{{ route('admin.powerbi.restore',$r->id) }}">
                    @csrf
                    <button class="w-full text-left px-3 py-2 rounded-lg text-maroon-700 hover:bg-slate-50"> Restore</button>
                  </form>
                  <button type="button" @click="open=false; confirmDelete=true"
                          class="w-full text-left px-3 py-2 rounded-lg text-rose-700 hover:bg-slate-50"> Hapus Permanen</button>
                @else
                  <a href="{{ route('admin.powerbi.edit',$r) }}" class="block px-3 py-2 rounded-lg hover:bg-slate-50"> Edit</a>
                  <a href="{{ $r->embed_url }}" target="_blank" class="block px-3 py-2 rounded-lg hover:bg-slate-50 text-maroon-700"> Open Embed</a>
                  <button type="button" @click="open=false; confirmDelete=true"
                          class="w-full text-left px-3 py-2 rounded-lg text-rose-700 hover:bg-slate-50"> Delete</button>
                @endif
              </div>
            </div>
          </div>

          {{-- hidden forms --}}
          <form x-ref="deleteFormSoft" class="hidden" method="POST" action="{{ route('admin.powerbi.destroy',$r) }}">
            @csrf @method('DELETE')
          </form>
        

          {{-- modal --}}
          <div x-cloak x-show="confirmDelete" x-transition.opacity.duration.200ms class="fixed inset-0 z-40" role="dialog" aria-modal="true">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-[2px]" @click="confirmDelete=false" x-transition.opacity.duration.200ms></div>
            <div class="absolute inset-0 flex items-center justify-center p-4">
              <div x-show="confirmDelete"
                   x-transition:enter="transition ease-out duration-200"
                   x-transition:enter-start="opacity-0 translate-y-2 scale-[0.98]"
                   x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                   x-transition:leave="transition ease-in duration-150"
                   x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                   x-transition:leave-end="opacity-0 translate-y-2 scale-[0.98]"
                   class="w-full max-w-md rounded-2xl bg-white/95 backdrop-blur-sm shadow-2xl ring-1 ring-slate-200/80 overflow-hidden"
                   x-data="{ ack:false, text:'' }">
                <div class="px-5 pt-5 pb-3 flex items-start gap-3">
                  <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-rose-600 to-rose-700 text-white flex items-center justify-center shadow-inner">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path d="M3 6h18" stroke-width="2"/><path d="M8 6v-1a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v1" stroke-width="2"/>
                      <rect x="5" y="6" width="14" height="14" rx="2" stroke-width="2"/><path d="M10 11v6M14 11v6" stroke-width="2"/>
                    </svg>
                  </div>
                  <div class="min-w-0">
                    <h3 class="text-base font-semibold text-slate-900">Konfirmasi Hapus</h3>
                    <p class="mt-0.5 text-sm text-slate-500">Tindakan ini tidak dapat dibatalkan.</p>
                  </div>
                  <button @click="confirmDelete=false" class="ml-auto rounded-lg p-1.5 text-slate-500 hover:bg-slate-100/70">
                    <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M18 6 6 18M6 6l12 12"/></svg>
                  </button>
                </div>

                <div class="px-5 pb-4 space-y-3">
                  <div class="rounded-xl ring-1 ring-slate-200 bg-slate-50/60 px-3 py-2 text-sm">
                    Hapus <span class="font-semibold text-slate-800">{{ $r->name }}</span>?
                  </div>
                  <label class="flex items-start gap-2 text-sm text-slate-700">
                    <input type="checkbox" class="mt-0.5 rounded border-slate-300 text-rose-600 focus:ring-rose-500" x-model="ack">
                    <span>Saya memahami konsekuensi penghapusan ini.</span>
                  </label>
                  <div>
                    <label class="block text-[13px] text-slate-500 mb-1">Ketik <span class="font-semibold text-slate-700">HAPUS</span> untuk konfirmasi</label>
                    <input type="text" x-model.trim="text" placeholder="HAPUS"
                           class="w-full rounded-xl border-slate-300 focus:border-rose-500 focus:ring-rose-500 text-sm"/>
                  </div>
                </div>

                <div class="h-px bg-gradient-to-r from-transparent via-slate-200 to-transparent"></div>

                <div class="px-5 py-4 flex items-center justify-end gap-2.5">
                  <button @click="confirmDelete=false"
                          class="px-3.5 py-2 rounded-xl text-slate-700 ring-1 ring-slate-200 hover:bg-slate-50 text-sm font-medium">
                    Batal
                  </button>
                  <button :class="(ack && text==='HAPUS') ? 'opacity-100' : 'opacity-50 cursor-not-allowed'"
                          :disabled="!(ack && text==='HAPUS')"
                          @click="( {{ $isDeleted ? '$refs.deleteFormHard' : '$refs.deleteFormSoft' }} ).submit()"
                          class="px-3.5 py-2 rounded-xl bg-gradient-to-r from-rose-600 to-rose-700 text-white text-sm font-semibold shadow hover:brightness-[1.03] active:scale-[0.99]">
                    Ya, Hapus
                  </button>
                </div>

              </div>
            </div>
          </div>
          {{-- /modal --}}
        </div>
      @empty
        <div class="p-10 text-center text-slate-600">Belum ada report.</div>
      @endforelse
    </div>

  </div>

</div>
@endsection
