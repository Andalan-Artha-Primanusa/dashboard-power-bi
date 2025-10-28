{{-- resources/views/admin/users/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')
@php
  use Illuminate\Support\Facades\Storage;

  $q         = request('q','');
  $divId     = request('division_id');
  $roleKey   = request('role');
  $siteId    = request('site_id');
  $divisions = $divisions ?? collect([]);
  $roles     = $roles     ?? collect([]);   // ['gm','manager','super_admin',...]
  $sites     = $sites     ?? collect([]);

  // Avatar resolver
  $avatarUrl = function($u) {
    if (!empty($u->photo_url)) return $u->photo_url;
    if (!empty($u->avatar_path)) return Storage::url($u->avatar_path);
    if (!empty($u->profile_photo_url)) return $u->profile_photo_url;
    $hash = md5(strtolower(trim($u->email ?? '')));
    return "https://www.gravatar.com/avatar/{$hash}?s=160&d=identicon";
  };
@endphp

<div class="rounded-3xl shadow ring-1 ring-slate-200 bg-white overflow-hidden">

  {{-- HEADER --}}
  <div class="px-6 py-7 text-white relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-r from-maroon-800 via-maroon-700 to-maroon-600"></div>
    <div class="absolute inset-0 opacity-25 bg-[radial-gradient(70%_70%_at_10%_10%,_rgba(255,255,255,0.5)_0%,_transparent_60%)]"></div>
    <div class="absolute -top-16 -right-16 size-64 rounded-full bg-white/10 blur-3xl"></div>

    <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
      <div class="text-white">
        <h1 class="text-2xl font-bold tracking-tight">ARCA — Manajemen User</h1>
        <p class="text-sm text-white/85 mt-1">Kelola akun, role, divisi, dan site.</p>
      </div>
      <a href="{{ route('admin.users.create') }}"
         class="inline-flex items-center gap-2 px-4 py-2 rounded-xl font-semibold shadow-sm hover:shadow bg-white text-maroon-900 ring-1 ring-white/20">
        ➕ Tambah User
      </a>
    </div>
  </div>

  {{-- FILTER BAR --}}
  <div class="px-6 py-4 border-b bg-white">
    <form method="GET" class="grid gap-3 sm:grid-cols-12 items-center">
      {{-- Search --}}
      <label class="sm:col-span-4 relative">
        <input name="q" value="{{ $q }}" placeholder="Cari nama atau email…"
               class="w-full rounded-xl border-slate-300 pl-10 pr-3 py-2.5 text-sm focus:ring-maroon-700 focus:border-maroon-700" />
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <circle cx="11" cy="11" r="7"/><path d="m21 21-3.5-3.5"/>
        </svg>
      </label>

      {{-- Division --}}
      <div class="sm:col-span-3">
        <select name="division_id"
                class="w-full rounded-xl border-slate-300 px-3 py-2.5 text-sm focus:ring-maroon-700 focus:border-maroon-700">
          <option value="">Semua Divisi</option>
          @foreach($divisions as $d)
            <option value="{{ $d->id }}" {{ (string)$divId===(string)$d->id?'selected':'' }}>{{ $d->name }}</option>
          @endforeach
        </select>
      </div>

      {{-- Role --}}
      <div class="sm:col-span-3">
        <select name="role"
                class="w-full rounded-xl border-slate-300 px-3 py-2.5 text-sm focus:ring-maroon-700 focus:border-maroon-700">
          <option value="">Semua Role</option>
          @foreach($roles as $rk => $rname)
            @php
              $val   = is_int($rk) ? $rname : $rk;
              $label = is_int($rk) ? ucfirst($rname) : $rname;
            @endphp
            <option value="{{ $val }}" {{ ($roleKey===$val)?'selected':'' }}>{{ $label }}</option>
          @endforeach
        </select>
      </div>

      {{-- Site --}}
      <div class="sm:col-span-2">
        <select name="site_id"
                class="w-full rounded-xl border-slate-300 px-3 py-2.5 text-sm focus:ring-maroon-700 focus:border-maroon-700">
          <option value="">Semua Site</option>
          @foreach($sites as $s)
            <option value="{{ $s->id }}" {{ (string)$siteId===(string)$s->id?'selected':'' }}>
              {{ $s->code }} — {{ $s->name }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="sm:col-span-12 sm:justify-self-end">
        <button class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-maroon-700 text-white text-sm font-semibold hover:bg-maroon-800 ring-1 ring-maroon-900/20">
          Terapkan
        </button>
      </div>
    </form>
  </div>

  {{-- ALERTS --}}
  @php
    $hasPwd    = session()->has('generated_password');
    $statusMsg = session('status') ?? session('message');
    $errs      = $errors->any() ? $errors->all() : [];
  @endphp
  <div class="px-6 pt-5">
    @if ($hasPwd)
      <div x-data="{open:true, copied:false}" x-show="open" x-transition
           class="relative mb-4 overflow-hidden rounded-2xl bg-white ring-1 ring-maroon-200 shadow-[inset_0_0_0_1px_rgba(128,0,32,0.15)]">
        <div class="bg-gradient-to-r from-maroon-800 via-maroon-700 to-maroon-600 px-4 py-2 text-white text-xs font-semibold tracking-wide">
          🔐 Password Baru — tampil sekali, segera salin
        </div>
        <div class="p-4 flex items-start gap-3">
          <svg class="h-5 w-5 text-maroon-700 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="2" d="M12 9v4m0 4h.01"/><circle cx="12" cy="12" r="10" stroke-width="2"/>
          </svg>
          <div class="text-sm text-slate-800">
            <div class="flex items-center gap-2 flex-wrap">
              <span class="text-slate-600">Password:</span>
              <code id="__pwd" class="px-2 py-1 rounded-lg bg-slate-100 ring-1 ring-slate-200 font-mono text-[13px]">
                {{ session('generated_password') }}
              </code>
              <button
                @click="navigator.clipboard.writeText(document.getElementById('__pwd').innerText.trim()); copied=true"
                class="inline-flex items-center gap-1 rounded-lg px-2 py-1 text-xs ring-1 ring-slate-300 hover:bg-slate-50">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <rect x="9" y="9" width="13" height="13" rx="2"/><rect x="2" y="2" width="13" height="13" rx="2"/>
                </svg>
                <span x-show="!copied">Copy</span>
                <span x-show="copied" class="text-maroon-700">Copied!</span>
              </button>
            </div>
            <p class="mt-1 text-xs text-slate-500">Jangan simpan di log. Minta user ganti password setelah login.</p>
          </div>
          <button @click="open=false" class="ml-auto rounded-lg p-1.5 text-slate-600 hover:bg-slate-100">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M18 6 6 18M6 6l12 12"/></svg>
          </button>
        </div>
      </div>
    @elseif ($statusMsg)
      <div x-data="{open:true}" x-show="open" x-transition
           class="relative mb-4 rounded-2xl border border-maroon-200 bg-maroon-50/70 ring-1 ring-maroon-100 shadow-sm">
        <div class="flex items-start gap-3 p-4">
          <svg class="h-5 w-5 text-maroon-700 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="2" d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="9"/>
          </svg>
          <div class="text-sm text-maroon-900">{{ $statusMsg }}</div>
          <button @click="open=false" class="ml-auto rounded-lg p-1.5 text-maroon-800/80 hover:bg-maroon-100/60">
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
            <path stroke-width="2" d="M12 9v4m0 4h.01"/><circle cx="12" cy="12" r="10" stroke-width="2"/>
          </svg>
          <div class="text-sm text-rose-900">
            <div class="font-semibold">Terjadi kesalahan:</div>
            <ul class="list-disc ml-5 mt-1 space-y-0.5">
              @foreach ($errs as $err)<li>{{ $err }}</li>@endforeach
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

  {{-- TABLE (desktop) --}}
  <div class="hidden md:block p-6 overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="sticky top-0 bg-slate-50 text-slate-600 text-xs font-semibold uppercase border-b">
        <tr>
          <th class="px-4 py-3 text-left">User</th>
          <th class="px-4 py-3 text-left">Divisi</th>
          <th class="px-4 py-3 text-left">Site</th>
          <th class="px-4 py-3 text-left">Role</th>
          <th class="px-4 py-3 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-200">
        @forelse($users as $u)
        <tr class="hover:bg-slate-50" x-data="{open:false, confirmReset:false, confirmDelete:false}">
          {{-- USER (with photo) --}}
          <td class="px-4 py-3">
            <div class="flex items-center gap-3 min-w-0">
              <img src="{{ $avatarUrl($u) }}" alt="{{ $u->name }}"
                   class="h-10 w-10 rounded-xl object-cover ring-1 ring-slate-200 shadow-sm">
              <div class="min-w-0">
                <div class="font-semibold text-slate-900 truncate">{{ $u->name }}</div>
                <div class="text-xs text-slate-500 truncate">{{ $u->email }}</div>
              </div>
            </div>
          </td>

          <td class="px-4 py-3">
            @if($u->division)
              <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-slate-100 text-slate-700 ring-1 ring-slate-200">
                {{ $u->division->name }}
              </span>
            @else
              <span class="text-slate-400">-</span>
            @endif
          </td>

          <td class="px-4 py-3">
            @if($u->defaultSite)
              <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-slate-100 text-maroon-900 ring-1 ring-slate-200">
                {{ $u->defaultSite->code }} — {{ $u->defaultSite->name }}
                @if($u->defaultSite->region)
                  <span class="ml-1 text-[10px] text-maroon-800/75">({{ $u->defaultSite->region }})</span>
                @endif
              </span>
            @else
              <span class="text-slate-400">-</span>
            @endif
          </td>

          <td class="px-4 py-3">
            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-maroon-100 text-maroon-800 ring-1 ring-maroon-200">
              {{ is_string($u->role ?? null) ? ucfirst($u->role) : (optional($u->role)->name ?? '-') }}
            </span>
          </td>

          <td class="px-4 py-3 text-right">
            <div class="relative inline-block text-left">
              <button @click="open=!open"
                      class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold ring-1 ring-slate-200">
                Actions
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
              </button>

              <div x-cloak x-show="open" @click.outside="open=false"
                   class="absolute right-0 z-20 mt-2 w-48 rounded-xl border border-slate-200 bg-white shadow-lg p-1 text-sm">
                <a href="{{ route('admin.users.edit',$u) }}" class="block px-3 py-2 rounded-lg hover:bg-slate-50">✏️ Edit</a>
                <button type="button" @click="open=false; confirmReset=true"
                        class="w-full text-left px-3 py-2 rounded-lg hover:bg-slate-50 text-maroon-700">🔐 Reset Password</button>
                <button type="button" @click="open=false; confirmDelete=true"
                        class="w-full text-left px-3 py-2 rounded-lg hover:bg-rose-50 text-rose-700">🗑️ Delete</button>
              </div>
            </div>

            {{-- Hidden forms --}}
            <form x-ref="resetForm" method="POST" action="{{ route('admin.users.resetPassword',$u) }}" class="hidden">@csrf</form>
            <form x-ref="deleteForm" method="POST" action="{{ route('admin.users.destroy',$u) }}" class="hidden">@csrf @method('DELETE')</form>

            {{-- MODAL: Reset --}}
            <div x-cloak x-show="confirmReset" x-transition.opacity.duration.200ms class="fixed inset-0 z-40"
                 role="dialog" aria-modal="true" aria-labelledby="resetTitle" @keydown.escape.window="confirmReset=false">
              <div class="absolute inset-0 bg-black/40 backdrop-blur-[2px]" @click="confirmReset=false"></div>
              <div class="absolute inset-0 flex items-center justify-center p-4">
                <div x-show="confirmReset"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2 scale-[0.98]"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-2 scale-[0.98]"
                     class="w-full max-w-md rounded-2xl bg-white/95 backdrop-blur-sm shadow-2xl ring-1 ring-slate-200/80 overflow-hidden"
                     x-data x-init="$nextTick(()=> $el.querySelector('[data-primary]').focus())">
                  <div class="px-5 pt-5 pb-3 flex items-start gap-3">
                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-maroon-700 to-maroon-800 text-white flex items-center justify-center shadow-inner">
                      <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <rect x="3" y="11" width="18" height="10" rx="2" stroke-width="2" />
                        <path d="M7 11V8a5 5 0 0110 0v3" stroke-width="2" />
                      </svg>
                    </div>
                    <div class="min-w-0">
                      <h3 id="resetTitle" class="text-base font-semibold text-slate-900">Konfirmasi Reset Password</h3>
                      <p class="mt-0.5 text-sm text-slate-500">Aksi ini akan membuat password baru untuk user berikut.</p>
                    </div>
                    <button @click="confirmReset=false" class="ml-auto rounded-lg p-1.5 text-slate-500 hover:bg-slate-100/70">
                      <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </button>
                  </div>
                  <div class="px-5 pb-4">
                    <div class="rounded-xl ring-1 ring-slate-200 bg-slate-50/60 px-3 py-2 text-sm">
                      Reset password untuk <span class="font-semibold text-slate-800">{{ $u->name }}</span>?
                    </div>
                    <p class="mt-2.5 text-[13px] text-slate-500">Password baru akan tampil sekali di halaman ini.</p>
                  </div>
                  <div class="h-px bg-gradient-to-r from-transparent via-slate-200 to-transparent"></div>
                  <div class="px-5 py-4 flex items-center justify-end gap-2.5">
                    <button @click="confirmReset=false"
                            class="px-3.5 py-2 rounded-xl text-slate-700 ring-1 ring-slate-200 hover:bg-slate-50 text-sm font-medium">
                      Batal
                    </button>
                    <button data-primary @click="$refs.resetForm.submit()"
                            class="px-3.5 py-2 rounded-xl bg-maroon-700 text-white text-sm font-semibold shadow hover:brightness-[1.03] active:scale-[0.99]">
                      Ya, Reset
                    </button>
                  </div>
                </div>
              </div>
            </div>

            {{-- MODAL: Delete --}}
            <div x-cloak x-show="confirmDelete" x-transition.opacity.duration.200ms class="fixed inset-0 z-40"
                 role="dialog" aria-modal="true" aria-labelledby="delTitle" @keydown.escape.window="confirmDelete=false">
              <div class="absolute inset-0 bg-black/40 backdrop-blur-[2px]" @click="confirmDelete=false"></div>
              <div class="absolute inset-0 flex items-center justify-center p-4">
                <div x-show="confirmDelete"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2 scale-[0.98]"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-2 scale-[0.98]"
                     class="w-full max-w-md rounded-2xl bg-white/95 backdrop-blur-sm shadow-2xl ring-1 ring-slate-200/80 overflow-hidden"
                     x-data="{ ack:false, text:'' }" x-init="$nextTick(()=> $el.querySelector('[data-primary]')?.focus())">
                  <div class="px-5 pt-5 pb-3 flex items-start gap-3">
                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-rose-600 to-red-600 text-white flex items-center justify-center shadow-inner">
                      <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M3 6h18" stroke-width="2"/><path d="M8 6v-1a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v1" stroke-width="2"/>
                        <rect x="5" y="6" width="14" height="14" rx="2" stroke-width="2"/><path d="M10 11v6M14 11v6" stroke-width="2"/>
                      </svg>
                    </div>
                    <div class="min-w-0">
                      <h3 id="delTitle" class="text-base font-semibold text-slate-900">Hapus User</h3>
                      <p class="mt-0.5 text-sm text-slate-500">Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                    <button @click="confirmDelete=false" class="ml-auto rounded-lg p-1.5 text-slate-500 hover:bg-slate-100/70">
                      <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </button>
                  </div>
                  <div class="px-5 pb-4 space-y-3">
                    <div class="rounded-xl ring-1 ring-slate-200 bg-slate-50/60 px-3 py-2 text-sm">
                      Hapus permanen <span class="font-semibold text-slate-800">{{ $u->name }}</span>?
                    </div>
                    <label class="flex items-start gap-2 text-sm text-slate-700">
                      <input type="checkbox" class="mt-0.5 rounded border-slate-300 text-rose-600 focus:ring-rose-500" x-model="ack">
                      <span>Saya memahami konsekuensi penghapusan permanen ini.</span>
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
                    <button data-primary
                            :class="(ack && text==='HAPUS') ? 'opacity-100' : 'opacity-50 cursor-not-allowed'"
                            :disabled="!(ack && text==='HAPUS')"
                            @click="$refs.deleteForm.submit()"
                            class="px-3.5 py-2 rounded-xl bg-gradient-to-r from-rose-600 to-red-600 text-white text-sm font-semibold shadow hover:brightness-[1.03] active:scale-[0.99]">
                      Ya, Hapus
                    </button>
                  </div>
                </div>
              </div>
            </div>

          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="px-4 py-16">
            <div class="mx-auto max-w-md text-center">
              <div class="mx-auto h-12 w-12 rounded-2xl bg-slate-100 flex items-center justify-center">
                <svg class="h-6 w-6 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
              </div>
              <h3 class="mt-4 text-lg font-semibold text-slate-800">Belum ada user</h3>
              <p class="mt-1 text-sm text-slate-500">Tambahkan user baru untuk mulai mengelola akses.</p>
              <a href="{{ route('admin.users.create') }}"
                 class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-xl font-semibold shadow-sm hover:shadow bg-maroon-700 text-white">
                + Tambah User
              </a>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>

    <div class="mt-5">
      {{ $users->appends(['q'=>$q,'division_id'=>$divId,'role'=>$roleKey,'site_id'=>$siteId])->links() }}
    </div>
  </div>

  {{-- MOBILE CARDS --}}
  <div class="md:hidden divide-y bg-white">
    @forelse($users as $u)
      <div class="p-4" x-data="{confirmReset:false, confirmDelete:false}">
        <div class="flex items-start justify-between gap-3">
          <div class="flex items-start gap-3 min-w-0">
            <img src="{{ $avatarUrl($u) }}" alt="{{ $u->name }}"
                 class="h-12 w-12 rounded-2xl object-cover ring-1 ring-slate-200 shadow-sm">
            <div class="min-w-0">
              <div class="font-semibold text-slate-900 truncate">{{ $u->name }}</div>
              <div class="text-xs text-slate-500 truncate">{{ $u->email }}</div>
              <div class="mt-1 text-xs space-x-1 space-y-1">
                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 ring-1 ring-slate-200">{{ $u->division->name ?? '-' }}</span>
                <span class="px-2 py-0.5 rounded-full bg-maroon-100 text-maroon-800 ring-1 ring-maroon-200">
                  {{ is_string($u->role ?? null) ? ucfirst($u->role) : (optional($u->role)->name ?? '-') }}
                </span>
                @if($u->defaultSite)
                  <span class="px-2 py-0.5 rounded-full bg-slate-100 text-maroon-900 ring-1 ring-slate-200">
                    {{ $u->defaultSite->code }} — {{ $u->defaultSite->name }}
                  </span>
                @else
                  <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-500">No Site</span>
                @endif
              </div>
            </div>
          </div>
          <div class="text-right space-y-2 shrink-0">
            <a href="{{ route('admin.users.edit',$u) }}"
               class="block px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 text-xs font-medium">✏️ Edit</a>
            <button type="button" @click="confirmReset=true"
                    class="w-full px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 text-xs font-medium">
              🔐 Reset
            </button>
            <button type="button" @click="confirmDelete=true"
                    class="w-full px-3 py-1.5 rounded-lg bg-rose-600 text-white hover:bg-rose-500 text-xs font-medium">
              🗑️ Delete
            </button>
          </div>
        </div>

        {{-- Hidden forms --}}
        <form x-ref="resetForm" method="POST" action="{{ route('admin.users.resetPassword',$u) }}" class="hidden">@csrf</form>
        <form x-ref="deleteForm" method="POST" action="{{ route('admin.users.destroy',$u) }}" class="hidden">@csrf @method('DELETE')</form>

        {{-- Modal: Reset (mobile) --}}
        <div x-cloak x-show="confirmReset" class="fixed inset-0 z-40" aria-modal="true" role="dialog">
          <div class="absolute inset-0 bg-black/40 backdrop-blur-[2px]" @click="confirmReset=false"></div>
          <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="w-full max-w-sm rounded-2xl bg-white/95 backdrop-blur-sm shadow-2xl ring-1 ring-slate-200 overflow-hidden">
              <div class="px-4 py-3 bg-gradient-to-r from-maroon-800 via-maroon-700 to-maroon-600 text-white text-sm font-semibold">
                Konfirmasi Reset Password
              </div>
              <div class="p-4 text-sm text-slate-700">
                Reset password untuk <span class="font-semibold">{{ $u->name }}</span>?
              </div>
              <div class="px-4 pb-4 flex justify-end gap-2">
                <button @click="confirmReset=false"
                        class="px-3 py-1.5 rounded-lg ring-1 ring-slate-200 text-slate-700 hover:bg-slate-50">Batal</button>
                <button @click="$refs.resetForm.submit()"
                        class="px-3 py-1.5 rounded-lg bg-maroon-700 text-white font-semibold hover:bg-maroon-800">Ya, Reset</button>
              </div>
            </div>
          </div>
        </div>

        {{-- Modal: Delete (mobile) --}}
        <div x-cloak x-show="confirmDelete" x-transition.opacity.duration.200ms class="fixed inset-0 z-40" role="dialog" aria-modal="true" aria-labelledby="delTitle">
          <div class="absolute inset-0 bg-black/40 backdrop-blur-[2px]" @click="confirmDelete=false"></div>
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
                <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-rose-600 to-red-600 text-white flex items-center justify-center shadow-inner">
                  <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M3 6h18" stroke-width="2"/><path d="M8 6v-1a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v1" stroke-width="2"/>
                    <rect x="5" y="6" width="14" height="14" rx="2" stroke-width="2"/><path d="M10 11v6M14 11v6" stroke-width="2"/>
                  </svg>
                </div>
                <div class="min-w-0">
                  <h3 id="delTitle" class="text-base font-semibold text-slate-900">Hapus User</h3>
                  <p class="mt-0.5 text-sm text-slate-500">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <button @click="confirmDelete=false" class="ml-auto rounded-lg p-1.5 text-slate-500 hover:bg-slate-100/70">
                  <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
              </div>
              <div class="px-5 pb-4 space-y-3">
                <div class="rounded-xl ring-1 ring-slate-200 bg-slate-50/60 px-3 py-2 text-sm">
                  Hapus permanen <span class="font-semibold text-slate-800">{{ $u->name }}</span>?
                </div>
                <label class="flex items-start gap-2 text-sm text-slate-700">
                  <input type="checkbox" class="mt-0.5 rounded border-slate-300 text-rose-600 focus:ring-rose-500" x-model="ack">
                  <span>Saya memahami konsekuensi penghapusan permanen ini.</span>
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
                <button
                        :class="(ack && text==='HAPUS') ? 'opacity-100' : 'opacity-50 cursor-not-allowed'"
                        :disabled="!(ack && text==='HAPUS')"
                        @click="$refs.deleteForm.submit()"
                        class="px-3.5 py-2 rounded-xl bg-gradient-to-r from-rose-600 to-red-600 text-white text-sm font-semibold shadow hover:brightness-[1.03] active:scale-[0.99]">
                  Ya, Hapus
                </button>
              </div>
            </div>
          </div>
        </div>

      </div>
    @empty
      <div class="p-10 text-center text-slate-600">Belum ada user.</div>
    @endforelse
  </div>
</div>
@endsection
