{{-- resources/views/admin/divisions/index.blade.php --}}
@extends('layouts.app')

@section('title','Daftar Divisi')

@section('content')
<div class="rounded-3xl overflow-hidden shadow ring-1 ring-slate-200 bg-white">

  {{-- HEADER (maroon-only, serumpun dgn Sites & PowerBI) --}}
  <div class="px-6 py-7 text-white relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-r from-maroon-800 via-maroon-700 to-maroon-600"></div>
    <div class="absolute inset-0 opacity-25 bg-[radial-gradient(70%_70%_at_10%_10%,_rgba(255,255,255,0.5)_0%,_transparent_60%)]"></div>
    <div class="absolute -top-16 -right-16 size-64 rounded-full bg-white/10 blur-3xl"></div>

    <div class="relative flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <h1 class="text-2xl font-bold tracking-tight">👥 Power BI-style • Divisions</h1>
        <p class="text-white/85 text-sm mt-1">Kelola daftar divisi, kode, status, dan ARCA</p>
      </div>
      <a href="{{ route('admin.divisions.create') }}"
         class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white text-maroon-900 font-semibold shadow hover:bg-slate-50">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 5v14M5 12h14"/></svg>
        Tambah Divisi
      </a>
    </div>
  </div>

  {{-- ALERTS (seragam) --}}
  @php
    $statusMsg = session('status') ?? session('message');
    $errs      = $errors->any() ? $errors->all() : [];
  @endphp
  <div class="px-6 pt-5">
    @if ($statusMsg)
      <div x-data="{open:true}" x-show="open" x-transition
           class="relative mb-4 rounded-2xl border border-maroon-200 bg-maroon-50/80 ring-1 ring-maroon-100 shadow-sm">
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
           class="relative mb-4 rounded-2xl border border-rose-200 bg-rose-50/80 ring-1 ring-rose-100 shadow-sm">
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
  </div>

  {{-- FILTER BAR (serumpun) --}}
  <div class="px-6 py-4 border-b bg-white">
    <form method="GET" class="grid gap-3 sm:grid-cols-12 items-center">
      <div class="sm:col-span-6 relative">
        <input type="text" name="q" value="{{ $q ?? '' }}"
               placeholder="Cari nama atau kode…"
               class="w-full rounded-xl border-slate-300 pl-10 pr-9 py-2.5 text-sm focus:ring-maroon-700 focus:border-maroon-700">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <circle cx="11" cy="11" r="7"/><path d="m21 21-3.5-3.5"/>
        </svg>
        @if(($q ?? '') !== '')
          <button type="button"
                  onclick="this.closest('form').querySelector('[name=q]').value=''; this.closest('form').submit()"
                  class="absolute right-2 top-1/2 -translate-y-1/2 p-1.5 rounded hover:bg-slate-100 text-slate-500">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M18 6 6 18M6 6l12 12"/></svg>
          </button>
        @endif
      </div>

      <div class="sm:col-span-3">
        <select name="status"
                class="w-full rounded-xl border-slate-300 py-2.5 text-sm focus:ring-maroon-700 focus:border-maroon-700">
          <option value="">Semua status</option>
          <option value="active"   {{ request('status')=='active'   ? 'selected' : '' }}>Aktif</option>
          <option value="inactive" {{ request('status')=='inactive' ? 'selected' : '' }}>Nonaktif</option>
        </select>
      </div>

      <label class="sm:col-span-2 inline-flex items-center gap-2 text-sm text-slate-600">
        <input type="checkbox" name="with_trashed" value="1"
               {{ ($showDel ?? false) ? 'checked' : '' }}
               onchange="this.form.submit()"
               class="rounded border-slate-300 text-maroon-700 focus:ring-maroon-600">
        Tampilkan yang dihapus
      </label>

      <div class="sm:col-span-1 sm:justify-self-end">
        <button type="submit"
                class="inline-flex items-center justify-center w-full gap-2 px-4 py-2 rounded-xl bg-maroon-700 text-white text-sm font-semibold hover:bg-maroon-800 ring-1 ring-maroon-900/20">
          Terapkan
        </button>
      </div>
    </form>

    {{-- Quick pills (optional, seragam) --}}
    @if(false)
    <div class="mt-3 flex items-center gap-2 text-xs">
      <a href="{{ route('admin.divisions.index') }}"
         class="px-3 py-1.5 rounded-lg font-semibold bg-maroon-700 text-white ring-1 ring-maroon-900 shadow-sm">Semua</a>
    </div>
    @endif
  </div>

  {{-- TABLE (desktop, seragam) --}}
  <div class="hidden md:block px-6 pb-6 overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 text-slate-600 text-xs font-semibold uppercase border-b">
        <tr>
          <th class="px-4 py-3 text-left">Name</th>
          <th class="px-4 py-3 text-left">Code</th>
          <th class="px-4 py-3 text-left">Status</th>
          <th class="px-4 py-3 text-right">Action</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-200">
        @forelse($divisions as $d)
          @php $isDeleted = $d->trashed(); @endphp
          <tr class="{{ $isDeleted ? 'bg-rose-50/40' : 'hover:bg-slate-50' }}"
              x-data="{ open:false, confirmDelete:false }">
            <td class="px-4 py-3">
              <div class="font-medium text-slate-800">{{ $d->name }}</div>
              @if($isDeleted)
                <span class="mt-1 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] bg-rose-100 text-rose-700 ring-1 ring-rose-200">
                  <span class="inline-block h-1.5 w-1.5 rounded-full bg-rose-500"></span> Terhapus
                </span>
              @endif
            </td>
            <td class="px-4 py-3 text-slate-600">{{ $d->code }}</td>
            <td class="px-4 py-3">
              @if($d->is_active)
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-maroon-50 text-maroon-900 ring-1 ring-maroon-200 text-xs font-medium">Active</span>
              @else
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-slate-200 text-slate-700 text-xs font-medium">Inactive</span>
              @endif
            </td>
            <td class="px-4 py-3 text-right">
              {{-- ACTIONS dropdown (SERAGAM) --}}
              <div class="relative inline-block text-left">
                <button @click="open=!open"
                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">
                  Actions
                  <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>

                <div x-cloak x-show="open" @click.outside="open=false"
                     class="absolute right-0 z-20 mt-2 w-48 rounded-xl border border-slate-200 bg-white shadow-lg py-1 text-sm">
                  @if($isDeleted)
                    <form method="POST" action="{{ route('admin.divisions.restore',$d->id) }}">
                      @csrf
                      <button class="w-full text-left px-3 py-2 hover:bg-slate-50 text-maroon-700">♻️ Restore</button>
                    </form>
                    <button type="button" @click="open=false; confirmDelete=true"
                            class="w-full text-left px-3 py-2 hover:bg-slate-50 text-rose-600">🗑 Hapus Permanen</button>
                  @else
                    <a href="{{ route('admin.divisions.edit',$d) }}" class="block px-3 py-2 hover:bg-slate-50">✏️ Edit</a>
                    <button type="button" @click="open=false; confirmDelete=true"
                            class="w-full text-left px-3 py-2 hover:bg-slate-50 text-rose-600">🗑 Delete</button>
                  @endif
                </div>
              </div>

              {{-- Hidden forms (soft & hard) --}}
              <form x-ref="deleteFormSoft-{{ $d->id }}" method="POST" action="{{ route('admin.divisions.destroy',$d) }}" class="hidden">
                @csrf @method('DELETE')
              </form>
             

              {{-- DELETE MODAL (SERAGAM) --}}
              <div x-cloak x-show="confirmDelete" x-transition.opacity.duration.200ms class="fixed inset-0 z-40" role="dialog" aria-modal="true" aria-labelledby="delTitle-{{ $d->id }}"
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
                        <h3 id="delTitle-{{ $d->id }}" class="text-base font-semibold text-slate-900">
                          @if($isDeleted) Hapus Permanen Divisi @else Hapus Divisi @endif
                        </h3>
                        <p class="mt-0.5 text-sm text-slate-500">Tindakan ini tidak dapat dibatalkan.</p>
                      </div>
                      <button data-cancel @click="confirmDelete=false" class="ml-auto rounded-lg p-1.5 text-slate-500 hover:bg-slate-100/70">
                        <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M18 6 6 18M6 6l12 12"/></svg>
                      </button>
                    </div>

                    <div class="px-5 pb-4 space-y-3">
                      <div class="rounded-xl ring-1 ring-slate-200 bg-slate-50/60 px-3 py-2 text-sm">
                        Hapus <span class="font-semibold text-slate-800">{{ $d->name }}</span>?
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
                              @click="( {{ $isDeleted ? '$refs[`deleteFormHard-'.$d->id.'`]' : '$refs[`deleteFormSoft-'.$d->id.'`]'}} ).submit()"
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
            <td colspan="4" class="px-4 py-12 text-center text-slate-500">Tidak ada divisi ditemukan.</td>
          </tr>
        @endforelse
      </tbody>
    </table>

    <div class="mt-6">
      {{ $divisions->onEachSide(1)->links() }}
    </div>
  </div>

  {{-- MOBILE CARDS (seragam) --}}
  <div class="md:hidden divide-y bg-white">
    @forelse($divisions as $d)
      @php $isDeleted = $d->trashed(); @endphp
      <div class="p-4" x-data="{ open:false, confirmDelete:false }">
        <div class="flex items-start justify-between gap-3">
          <div>
            <div class="font-semibold text-slate-900">{{ $d->name }}</div>
            <div class="text-xs text-slate-500">Kode: <span class="font-mono">{{ $d->code }}</span></div>
            <div class="mt-1">
              @if($d->is_active)
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-maroon-50 text-maroon-900 text-[11px] ring-1 ring-maroon-200">Active</span>
              @else
                <span class="inline-flex items-center px-3 py-1 rounded-full bg-slate-200 text-slate-700 text-[11px]">Inactive</span>
              @endif
              @if($isDeleted)
                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-[11px] bg-rose-100 text-rose-700 ring-1 ring-rose-200">Terhapus</span>
              @endif
            </div>
          </div>

          {{-- ACTIONS (seragam) --}}
          <div class="relative">
            <button @click="open=!open" class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 text-xs font-semibold hover:bg-slate-200">
              Actions
              <svg class="inline h-3.5 w-3.5 ml-1 align-[-2px]" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-cloak x-show="open" @click.outside="open=false"
                 class="absolute right-0 mt-2 w-48 rounded-xl border bg-white shadow-lg py-1 text-sm">
              @if($isDeleted)
                <form method="POST" action="{{ route('admin.divisions.restore',$d->id) }}">
                  @csrf
                  <button class="w-full text-left px-3 py-2 hover:bg-slate-50 text-maroon-700">♻️ Restore</button>
                </form>
                <button type="button" @click="open=false; confirmDelete=true"
                        class="w-full text-left px-3 py-2 hover:bg-slate-50 text-rose-600">🗑 Hapus Permanen</button>
              @else
                <a href="{{ route('admin.divisions.edit',$d) }}" class="block px-3 py-2 hover:bg-slate-50">✏️ Edit</a>
                <button type="button" @click="open=false; confirmDelete=true"
                        class="w-full text-left px-3 py-2 hover:bg-slate-50 text-rose-600">🗑 Delete</button>
              @endif
            </div>
          </div>
        </div>

        {{-- Hidden forms --}}
        <form x-ref="deleteFormSoft-{{ $d->id }}" method="POST" action="{{ route('admin.divisions.destroy',$d) }}" class="hidden">
          @csrf @method('DELETE')
        </form>
       
        {{-- Modal --}}
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
                  Hapus <span class="font-semibold text-slate-800">{{ $d->name }}</span>?
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
                <button @click="confirmDelete=false"
                        class="px-3.5 py-2 rounded-xl text-slate-700 ring-1 ring-slate-200 hover:bg-slate-50 text-sm font-medium">
                  Batal
                </button>
                <button :class="(ack && text==='HAPUS') ? 'opacity-100' : 'opacity-50 cursor-not-allowed'"
                        :disabled="!(ack && text==='HAPUS')"
                        @click="( {{ $isDeleted ? '$refs[`deleteFormHard-'.$d->id.'`]' : '$refs[`deleteFormSoft-'.$d->id.'`]'}} ).submit()"
                        class="px-3.5 py-2 rounded-xl bg-gradient-to-r from-rose-600 to-rose-700 text-white text-sm font-semibold shadow hover:brightness-[1.03] active:scale-[0.99]">
                  Ya, Hapus
                </button>
              </div>

            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="p-10 text-center text-slate-600">Tidak ada divisi ditemukan.</div>
    @endforelse
  </div>

</div>
@endsection
