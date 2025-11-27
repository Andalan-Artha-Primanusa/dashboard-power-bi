{{-- resources/views/admin/sites/index.blade.php --}}
@extends('layouts.app')

@section('title','Daftar Sites')

@section('content')
@php
  // dari controller (SiteAdminController@index)
  $companies = $companies ?? collect();
  $companyId = $companyId ?? request('company_id') ?? session('company_id');
  $only      = $only ?? request('only');   // 'trashed' atau null
  $q         = $q ?? request('q');
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
          <span>ARCA — Master Data</span>
        </div>
        <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight">
          Sites Management
        </h1>
        <p class="text-xs sm:text-sm text-white/80 max-w-xl">
          Kelola site per perusahaan, status aktif, serta pemulihan site yang terhapus (trash).
        </p>
      </div>

      <div class="flex flex-wrap gap-3 justify-start md:justify-end">
        <a href="{{ route('admin.sites.create', array_filter(['company_id'=>$companyId])) }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white text-maroon-900 font-semibold text-sm shadow hover:bg-slate-50">
          <span class="inline-flex h-5 w-5 items-center justify-center rounded-lg bg-maroon-100 text-maroon-800 text-[11px]">＋</span>
          <span>Tambah Site</span>
        </a>
      </div>
    </div>
  </div>

  {{-- ALERTS --}}
  @php
    $statusMsg = session('success') ?? session('status') ?? session('message');
    $errs      = $errors->any() ? $errors->all() : [];
  @endphp

  @if ($statusMsg)
    <div x-data="{open:true}" x-show="open" x-transition
         class="relative rounded-2xl border border-maroon-200 bg-maroon-50/80 ring-1 ring-maroon-100 shadow-sm">
      <div class="flex items-start gap-3 p-4">
        <svg class="h-5 w-5 text-maroon-700 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <circle cx="12" cy="12" r="9"/><path stroke-width="2" d="M9 12l2 2 4-4"/>
        </svg>
        <div class="text-sm text-maroon-900">{{ $statusMsg }}</div>
        <button @click="open=false" class="ml-auto rounded-lg p-1.5 text-maroon-800/80 hover:bg-maroon-100/50">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M18 6 6 18M6 6l12 12"/>
          </svg>
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
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M18 6 6 18M6 6l12 12"/>
          </svg>
        </button>
      </div>
      <div class="h-1 w-full bg-gradient-to-r from-rose-500 via-rose-400 to-rose-600"></div>
    </div>
  @endif

  {{-- FILTERS (disamakan dgn Companies / Users) --}}
  <form method="GET" action="{{ route('admin.sites.index') }}"
        class="relative overflow-hidden rounded-2xl border border-slate-300 bg-slate-50/80 shadow-sm">
    {{-- accent bar kiri --}}
    <div class="absolute inset-y-0 left-0 w-1 bg-gradient-to-b from-maroon-700 via-maroon-600 to-maroon-700"></div>

    <div class="px-4 py-4 sm:px-5 sm:py-4">
      {{-- header filter + garis --}}
      <div class="mb-3 flex items-center justify-between">
        <div class="flex items-center gap-2 w-full">
          <span class="text-[11px] font-semibold uppercase tracking-wide text-slate-700">
            Filter Sites
          </span>
          <span class="hidden sm:block flex-1 h-px bg-slate-200"></span>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
        {{-- Perusahaan --}}
        <div class="md:col-span-4">
          <label class="text-xs font-semibold text-slate-700">Perusahaan</label>
          <select name="company_id"
                  class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm
                         focus:border-maroon-700 focus:ring-2 focus:ring-maroon-700/80">
            <option value="">🏭 Semua Perusahaan</option>
            @foreach($companies as $c)
              <option value="{{ $c->id }}" @selected($companyId==$c->id)>
                {{ $c->code }} — {{ $c->name }}
              </option>
            @endforeach
          </select>
        </div>

        {{-- Mode listing (aktif / trash) --}}
        <div class="md:col-span-3">
          <label class="text-xs font-semibold text-slate-700">Mode Listing</label>
          <select name="only"
                  class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm
                         focus:border-maroon-700 focus:ring-2 focus:ring-maroon-700/80">
            <option value="" @selected($only !== 'trashed')>Aktif</option>
            <option value="trashed" @selected($only === 'trashed')>Terhapus (Trash)</option>
          </select>
        </div>

        {{-- Search --}}
        <div class="md:col-span-3">
          <label class="text-xs font-semibold text-slate-700">Search</label>
          <div class="mt-1 relative">
            <input type="text" name="q" value="{{ $q ?? '' }}"
                   placeholder="Cari kode atau nama…"
                   class="w-full rounded-xl border border-slate-300 bg-white pl-9 pr-3 py-2.5 text-sm
                          focus:border-maroon-700 focus:ring-2 focus:ring-maroon-700/80">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400"
                 viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <circle cx="11" cy="11" r="7"/><path d="m21 21-3.5-3.5"/>
            </svg>
          </div>
        </div>

        {{-- Actions --}}
        <div class="md:col-span-2 flex items-center gap-2 mt-3 md:mt-0">
          <button class="w-full md:w-auto px-4 py-2 rounded-xl bg-maroon-700 text-white text-sm font-semibold hover:bg-maroon-800 ring-1 ring-maroon-900/20">
            Apply
          </button>
          <a href="{{ route('admin.sites.index') }}"
             class="w-full md:w-auto px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 text-center">
            Reset
          </a>
        </div>
      </div>
    </div>
  </form>

  {{-- DATA CARD (table desktop + kartu mobile) --}}
  <div class="rounded-2xl bg-white ring-1 ring-slate-200 shadow-sm overflow-hidden">

    {{-- TABLE (md+) --}}
    <div class="hidden md:block px-6 pb-6 pt-4 overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50 text-slate-600 text-xs font-semibold uppercase border-b">
          <tr>
            <th class="px-4 py-3 text-left">Company</th>
            <th class="px-4 py-3 text-left">Code</th>
            <th class="px-4 py-3 text-left">Name</th>
            <th class="px-4 py-3 text-left">Region</th>
            <th class="px-4 py-3 text-left">Status</th>
            <th class="px-4 py-3 text-right">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
          @forelse($sites as $s)
            <tr class="{{ (method_exists($s,'trashed') && $s->trashed()) ? 'bg-rose-50/40' : 'hover:bg-slate-50' }}"
                x-data="{ open:false, confirmDelete:false }">

              {{-- Company col --}}
              <td class="px-4 py-3">
                @php $comp = $s->company ?? null; @endphp
                @if($comp)
                  <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-slate-100 text-slate-700 ring-1 ring-slate-200 text-[11px] font-semibold">
                    🏭 {{ $comp->code }}
                  </span>
                  <div class="text-xs text-slate-500 mt-0.5 line-clamp-1">{{ $comp->name }}</div>
                @else
                  <span class="text-xs text-slate-400">—</span>
                @endif
              </td>

              <td class="px-4 py-3 font-mono text-[13px] text-slate-800">{{ $s->code }}</td>
              <td class="px-4 py-3 font-medium text-slate-900">{{ $s->name }}</td>
              <td class="px-4 py-3 text-slate-500">{{ $s->region ?: '—' }}</td>

              <td class="px-4 py-3">
                @if(method_exists($s,'trashed') && $s->trashed())
                  <span class="inline-flex items-center px-3 py-1 rounded-full bg-slate-200 text-slate-700 text-xs font-medium">Trashed</span>
                @else
                  @if ($s->is_active)
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-maroon-50 text-maroon-900 ring-1 ring-maroon-200 text-xs font-medium">Active</span>
                  @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-slate-200 text-slate-700 text-xs font-medium">Inactive</span>
                  @endif
                @endif
              </td>

              <td class="px-4 py-3 text-right">
                {{-- ACTIONS dropdown --}}
                <div class="relative inline-block text-left">
                  <button @click="open=!open"
                          class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">
                    Actions
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                  </button>

                  <div x-cloak x-show="open" @click.outside="open=false"
                       class="absolute right-0 z-20 mt-2 w-48 rounded-xl border border-slate-200 bg-white shadow-lg py-1 text-sm">
                    @if(method_exists($s,'trashed') && $s->trashed())
                      <form method="POST" action="{{ route('admin.sites.restore',$s->id) }}">
                        @csrf
                        <button class="w-full text-left px-3 py-2 hover:bg-slate-50 text-maroon-700">♻️ Restore</button>
                      </form>
                      <button type="button" @click="open=false; confirmDelete=true"
                              class="w-full text-left px-3 py-2 hover:bg-slate-50 text-rose-600">🗑 Hapus Permanen</button>
                    @else
                      <a href="{{ route('admin.sites.edit',$s) }}" class="block px-3 py-2 hover:bg-slate-50">✏️ Edit</a>
                      <form method="POST" action="{{ route('admin.sites.toggle',$s) }}">
                        @csrf @method('PATCH')
                        <button class="w-full text-left px-3 py-2 hover:bg-slate-50">🔁 Toggle Active</button>
                      </form>
                      <button type="button" @click="open=false; confirmDelete=true"
                              class="w-full text-left px-3 py-2 hover:bg-slate-50 text-rose-600">🗑 Delete</button>
                    @endif
                  </div>
                </div>

                {{-- Hidden forms --}}
                <form x-ref="deleteFormSoft" class="hidden" method="POST" action="{{ route('admin.sites.destroy',$s) }}">
                  @csrf @method('DELETE')
                </form>
                <form x-ref="deleteFormHard" class="hidden" method="POST" action="{{ route('admin.sites.forceDelete',$s->id) }}">
                  @csrf @method('DELETE')
                </form>

                {{-- DELETE MODAL (desktop) --}}
                <div x-cloak x-show="confirmDelete" x-transition.opacity.duration.200ms class="fixed inset-0 z-40" role="dialog" aria-modal="true"
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
                         x-data="{ ack:false, text:'' }">

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
                          <h3 class="text-base font-semibold text-slate-900">
                            @if(method_exists($s,'trashed') && $s->trashed()) Hapus Permanen Site @else Hapus Site @endif
                          </h3>
                          <p class="mt-0.5 text-sm text-slate-500">Tindakan ini tidak dapat dibatalkan.</p>
                        </div>
                        <button @click="confirmDelete=false" class="ml-auto rounded-lg p-1.5 text-slate-500 hover:bg-slate-100/70">
                          <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M18 6 6 18M6 6l12 12"/></svg>
                        </button>
                      </div>

                      <div class="px-5 pb-4 space-y-3">
                        <div class="rounded-xl ring-1 ring-slate-200 bg-slate-50/60 px-3 py-2 text-sm">
                          Hapus <span class="font-semibold text-slate-800">{{ $s->name }}</span>?
                          @if(method_exists($s,'trashed') && $s->trashed())
                            <span class="text-rose-600 font-semibold"> (PERMANEN)</span>
                          @endif
                        </div>
                        <label class="flex items-start gap-2 text-sm text-slate-700">
                          <input type="checkbox" class="mt-0.5 rounded border-slate-300 text-rose-600 focus:ring-rose-500" x-model="ack">
                          <span>Saya memahami konsekuensi penghapusan ini.</span>
                        </label>
                        <div>
                          <label class="block text-[13px] text-slate-500 mb-1">
                            Ketik <span class="font-semibold text-slate-700">HAPUS</span> untuk konfirmasi
                          </label>
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
                                @click="( {{ (method_exists($s,'trashed') && $s->trashed()) ? '$refs.deleteFormHard' : '$refs.deleteFormSoft' }} ).submit()"
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
              <td colspan="6" class="px-4 py-12 text-center text-slate-500">
                Belum ada data. <a href="{{ route('admin.sites.create') }}" class="text-maroon-700 font-semibold hover:underline">Tambah Site</a>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>

      <div class="mt-6">
        {{ $sites->withQueryString()->links() }}
      </div>
    </div>

    {{-- MOBILE CARDS --}}
    <div class="md:hidden divide-y bg-white">
      @forelse($sites as $s)
        <div class="p-4" x-data="{ open:false, confirmDelete:false }">
          <div class="flex items-start justify-between gap-3">
            <div>
              <div class="font-semibold text-slate-900">{{ $s->name }}</div>
              <div class="text-xs text-slate-500">
                Kode: <span class="font-mono">{{ $s->code }}</span>
              </div>

              @php $comp = $s->company ?? null; @endphp
              @if($comp)
                <div class="mt-1">
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 ring-1 ring-slate-200 text-[11px] font-semibold">
                    🏭 {{ $comp->code }} — {{ $comp->name }}
                  </span>
                </div>
              @endif

              <div class="mt-1">
                @if(method_exists($s,'trashed') && $s->trashed())
                  <span class="inline-flex items-center px-3 py-1 rounded-full bg-slate-200 text-slate-700 text-[11px] font-medium">Trashed</span>
                @else
                  @if ($s->is_active)
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-maroon-50 text-maroon-900 text-[11px] ring-1 ring-maroon-200 font-medium">Active</span>
                  @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-slate-200 text-slate-700 text-[11px] font-medium">Inactive</span>
                  @endif
                @endif
              </div>
            </div>

            <div class="relative">
              <button @click="open=!open" class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 text-xs font-semibold hover:bg-slate-200">
                Actions
                <svg class="inline h-3.5 w-3.5 ml-1 align-[-2px]" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
              </button>
              <div x-cloak x-show="open" @click.outside="open=false"
                   class="absolute right-0 mt-2 w-48 rounded-xl border bg-white shadow-lg py-1 text-sm">
                @if(method_exists($s,'trashed') && $s->trashed())
                  <form method="POST" action="{{ route('admin.sites.restore',$s->id) }}">
                    @csrf
                    <button class="w-full text-left px-3 py-2 hover:bg-slate-50 text-maroon-700">♻️ Restore</button>
                  </form>
                  <button type="button" @click="open=false; confirmDelete=true"
                          class="w-full text-left px-3 py-2 hover:bg-slate-50 text-rose-600">🗑 Hapus Permanen</button>
                @else
                  <a href="{{ route('admin.sites.edit',$s) }}" class="block px-3 py-2 hover:bg-slate-50">✏️ Edit</a>
                  <form method="POST" action="{{ route('admin.sites.toggle',$s) }}">
                    @csrf @method('PATCH')
                    <button class="w-full text-left px-3 py-2 hover:bg-slate-50">🔁 Toggle Active</button>
                  </form>
                  <button type="button" @click="open=false; confirmDelete=true"
                          class="w-full text-left px-3 py-2 hover:bg-slate-50 text-rose-600">🗑 Delete</button>
                @endif
              </div>
            </div>
          </div>

          {{-- Hidden forms --}}
          <form x-ref="deleteFormSoft" class="hidden" method="POST" action="{{ route('admin.sites.destroy',$s) }}">
            @csrf @method('DELETE')
          </form>
          <form x-ref="deleteFormHard" class="hidden" method="POST" action="{{ route('admin.sites.forceDelete',$s->id) }}">
            @csrf @method('DELETE')
          </form>

          {{-- Modal sederhana (mobile) --}}
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
                    Hapus <span class="font-semibold text-slate-800">{{ $s->name }}</span>?
                  </div>
                  <label class="flex items-start gap-2 text-sm text-slate-700">
                    <input type="checkbox" class="mt-0.5 rounded border-slate-300 text-rose-600 focus:ring-rose-500" x-model="ack">
                    <span>Saya memahami konsekuensi penghapusan ini.</span>
                  </label>
                  <div>
                    <label class="block text-[13px] text-slate-500 mb-1">
                      Ketik <span class="font-semibold text-slate-700">HAPUS</span> untuk konfirmasi
                    </label>
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
                          @click="( {{ (method_exists($s,'trashed') && $s->trashed()) ? '$refs.deleteFormHard' : '$refs.deleteFormSoft' }} ).submit()"
                          class="px-3.5 py-2 rounded-xl bg-gradient-to-r from-rose-600 to-rose-700 text-white text-sm font-semibold shadow hover:brightness-[1.03] active:scale-[0.99]">
                    Ya, Hapus
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      @empty
        <div class="p-10 text-center text-slate-600">Tidak ada site ditemukan.</div>
      @endforelse

      <div class="p-4">
        {{ $sites->withQueryString()->links() }}
      </div>
    </div>

  </div>

</div>
@endsection
