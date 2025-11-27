{{-- resources/views/admin/users/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')
@php
  use Illuminate\Support\Facades\Storage;

  $q         = request('q','');
  $companyId = request('company_id'); // filter company
  $divId     = request('division_id');
  $roleKey   = request('role');
  $siteId    = request('site_id');

  $companies = $companies ?? collect([]);
  $divisions = $divisions ?? collect([]);
  $roles     = $roles     ?? collect([]);
  $sites     = $sites     ?? collect([]);

  // Avatar resolver
  $avatarUrl = function($u) {
    if (!empty($u->photo_url)) return $u->photo_url;
    if (!empty($u->avatar_path)) return Storage::url($u->avatar_path);
    if (!empty($u->profile_photo_url)) return $u->profile_photo_url;
    $hash = md5(strtolower(trim($u->email ?? '')));
    return "https://www.gravatar.com/avatar/{$hash}?s=160&d=identicon";
  };

  $hasPwd    = session()->has('generated_password');
  $statusMsg = session('status') ?? session('message') ?? session('success');
  $errs      = $errors->any() ? $errors->all() : [];
@endphp

<div class="w-full space-y-5">

  {{-- HEADER MAROON (serumpun ARCA) --}}
  <div class="relative overflow-hidden rounded-3xl ring-1 ring-white/40 bg-gradient-to-r from-maroon-800 via-maroon-700 to-maroon-600">
    <div class="absolute inset-0 opacity-25 bg-[radial-gradient(90%_70%_at_0%_0%,_rgba(255,255,255,0.55),_transparent_60%)]"></div>
    <div class="absolute -top-16 -right-16 size-64 rounded-full bg-white/10 blur-3xl"></div>

    <div class="relative px-6 py-6 sm:px-8 sm:py-7 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 text-white">
      <div class="space-y-1">
        <div class="inline-flex items-center gap-2 text-[11px] font-semibold text-white/85 uppercase tracking-wide">
          <span class="inline-flex h-6 w-6 items-center justify-center rounded-xl bg-white/20 ring-1 ring-white/30">
            <img src="{{ asset('assets/images/logoarca.png') }}" alt="ARCA" class="h-4 w-4 object-contain">
          </span>
          <span>ARCA — Access Control</span>
        </div>
        <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight">
          Manajemen User
        </h1>
        <p class="text-xs sm:text-sm text-white/80 max-w-xl">
          Kelola akun, role, divisi, company, dan site untuk semua pengguna ARCA.
        </p>
      </div>

      <div class="flex flex-wrap gap-3 justify-start sm:justify-end">
        <a href="{{ route('admin.users.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl font-semibold shadow bg-white text-maroon-900 hover:bg-slate-50 ring-1 ring-white/20">
          <span class="inline-flex h-5 w-5 items-center justify-center rounded-lg bg-maroon-100 text-maroon-800 text-[11px]">＋</span>
          <span>Tambah User</span>
        </a>
      </div>
    </div>
  </div>

  {{-- ALERTS (password / status / error) --}}
  @if ($hasPwd)
    <div x-data="{open:true, copied:false}" x-show="open" x-transition
         class="relative overflow-hidden rounded-2xl bg-white ring-1 ring-maroon-200 shadow-[inset_0_0_0_1px_rgba(128,0,32,0.15)]">
      <div class="bg-gradient-to-r from-maroon-800 via-maroon-700 to-maroon-600 px-4 py-2 text-white text-xs font-semibold tracking-wide">
        🔐 Password Baru — tampil sekali, segera salin
      </div>
      <div class="p-4 flex items-start gap-3">
        <svg class="h-5 w-5 text-maroon-700 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <circle cx="12" cy="12" r="10" stroke-width="2"/>
          <path stroke-width="2" d="M12 9v4m0 4h.01"/>
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
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M18 6 6 18M6 6l12 12"/>
          </svg>
        </button>
      </div>
    </div>
  @elseif ($statusMsg)
    <div x-data="{open:true}" x-show="open" x-transition
         class="relative rounded-2xl border border-maroon-200 bg-maroon-50/70 ring-1 ring-maroon-100 shadow-sm">
      <div class="flex items-start gap-3 p-4">
        <svg class="h-5 w-5 text-maroon-700 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-width="2" d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="9"/>
        </svg>
        <div class="text-sm text-maroon-900">{{ $statusMsg }}</div>
        <button @click="open=false" class="ml-auto rounded-lg p-1.5 text-maroon-800/80 hover:bg-maroon-100/60">
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
          <circle cx="12" cy="12" r="10" stroke-width="2"/>
          <path stroke-width="2" d="M12 9v4m0 4h.01"/>
        </svg>
        <div class="text-sm text-rose-900">
          <div class="font-semibold">Terjadi kesalahan:</div>
          <ul class="list-disc ml-5 mt-1 space-y-0.5">
            @foreach ($errs as $err)<li>{{ $err }}</li>@endforeach
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

  {{-- FILTER CARD (dipertebal bordernya) --}}
  <form method="GET"
        class="relative overflow-hidden rounded-2xl border border-slate-300 bg-slate-50/80 shadow-sm">
    {{-- accent bar kiri --}}
    <div class="absolute inset-y-0 left-0 w-1 bg-gradient-to-b from-maroon-700 via-maroon-600 to-maroon-700"></div>

    <div class="px-4 py-4 sm:px-5 sm:py-4">
      <div class="mb-3 flex items-center justify-between">
        <div class="flex items-center gap-2 w-full">
          <span class="text-[11px] font-semibold uppercase tracking-wide text-slate-700">
            Filter User
          </span>
          <span class="hidden sm:block flex-1 h-px bg-slate-200"></span>
        </div>
      </div>

      <div class="grid gap-3 lg:grid-cols-12 items-end">
        {{-- Search --}}
        <div class="lg:col-span-4">
          <label class="text-xs font-semibold text-slate-700">Search</label>
          <div class="mt-1 relative">
            <input name="q" value="{{ $q }}" placeholder="Cari nama atau email…"
                   class="w-full rounded-xl border border-slate-300 bg-white pl-10 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-maroon-700/80 focus:border-maroon-700" />
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <circle cx="11" cy="11" r="7"/><path d="m21 21-3.5-3.5"/>
            </svg>
          </div>
        </div>

        {{-- Company --}}
        <div class="lg:col-span-3">
          <label class="text-xs font-semibold text-slate-700">Company</label>
          <select name="company_id"
                  class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-maroon-700/80 focus:border-maroon-700">
            <option value="">Semua Company</option>
            @foreach($companies as $c)
              <option value="{{ $c->id }}" {{ (string)$companyId===(string)$c->id?'selected':'' }}>
                {{ $c->code ?? '' }} {{ $c->code ? '—' : '' }} {{ $c->name }}
              </option>
            @endforeach
          </select>
        </div>

        {{-- Division --}}
        <div class="lg:col-span-3">
          <label class="text-xs font-semibold text-slate-700">Divisi</label>
          <select name="division_id"
                  class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-maroon-700/80 focus:border-maroon-700">
            <option value="">Semua Divisi</option>
            @foreach($divisions as $d)
              <option value="{{ $d->id }}" {{ (string)$divId===(string)$d->id?'selected':'' }}>{{ $d->name }}</option>
            @endforeach
          </select>
        </div>

        {{-- Role --}}
        <div class="lg:col-span-2">
          <label class="text-xs font-semibold text-slate-700">Role</label>
          <select name="role"
                  class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-maroon-700/80 focus:border-maroon-700">
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
        <div class="lg:col-span-3">
          <label class="text-xs font-semibold text-slate-700">Site</label>
          <select name="site_id"
                  class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-maroon-700/80 focus:border-maroon-700">
            <option value="">Semua Site</option>
            @foreach($sites as $s)
              <option value="{{ $s->id }}" {{ (string)$siteId===(string)$s->id?'selected':'' }}>
                {{ $s->code }} — {{ $s->name }}
              </option>
            @endforeach
          </select>
        </div>

        {{-- Button --}}
        <div class="lg:col-span-2 flex items-center justify-end">
          <button class="w-full lg:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-maroon-700 text-white text-sm font-semibold hover:bg-maroon-800 ring-1 ring-maroon-900/20">
            Terapkan
          </button>
        </div>
      </div>
    </div>
  </form>

  {{-- DATA CARD (table + mobile) --}}
  <div class="rounded-2xl overflow-hidden shadow ring-1 ring-slate-200 bg-white">

    {{-- TABLE (desktop) --}}
    <div class="hidden md:block p-6 overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50 text-slate-600 text-xs font-semibold uppercase border-b">
          <tr>
            <th class="px-4 py-3 text-left">User</th>
            <th class="px-4 py-3 text-left">Company</th>
            <th class="px-4 py-3 text-left">Divisi</th>
            <th class="px-4 py-3 text-left">Site</th>
            <th class="px-4 py-3 text-left">Role</th>
            <th class="px-4 py-3 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
          @forelse($users as $u)
          <tr class="hover:bg-slate-50" x-data="{open:false, confirmReset:false, confirmDelete:false}">
            {{-- USER --}}
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

            {{-- COMPANY --}}
            <td class="px-4 py-3">
              @php
                $c = $u->defaultCompany ?? null;
              @endphp
              @if($c)
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-emerald-50 text-emerald-800 ring-1 ring-emerald-200">
                  {{ $c->code ?? '' }} {{ $c->code ? '—' : '' }} {{ $c->name }}
                </span>
              @elseif(!empty($u->default_company_id))
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-slate-100 text-slate-700 ring-1 ring-slate-200">
                  {{ $u->default_company_id }}
                </span>
              @else
                <span class="text-slate-400">-</span>
              @endif
            </td>

            {{-- DIVISION --}}
            <td class="px-4 py-3">
              @if($u->division)
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-slate-100 text-slate-700 ring-1 ring-slate-200">
                  {{ $u->division->name }}
                </span>
              @else
                <span class="text-slate-400">-</span>
              @endif
            </td>

            {{-- SITE --}}
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

            {{-- ROLE --}}
            <td class="px-4 py-3">
              <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-maroon-100 text-maroon-800 ring-1 ring-maroon-200">
                {{ is_string($u->role ?? null) ? ucfirst($u->role) : (optional($u->role)->name ?? '-') }}
              </span>
            </td>

            {{-- AKSI --}}
            <td class="px-4 py-3 text-right">
              <div class="relative inline-block text-left">
                <button @click="open=!open"
                        class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold ring-1 ring-slate-200">
                  Actions
                  <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-width="2" d="M19 9l-7 7-7-7"/>
                  </svg>
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

              {{-- MODAL Reset + Delete (lanjutkan punyamu di sini) --}}
              {{-- ... --}}
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="6" class="px-4 py-16">
              <div class="mx-auto max-w-md text-center">
                <div class="mx-auto h-12 w-12 rounded-2xl bg-slate-100 flex items-center justify-center">
                  <svg class="h-6 w-6 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/>
                  </svg>
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
        {{ $users->appends([
            'q'=>$q,
            'company_id'=>$companyId,
            'division_id'=>$divId,
            'role'=>$roleKey,
            'site_id'=>$siteId
        ])->links() }}
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
                  {{-- company chip --}}
                  @if($u->defaultCompany ?? null)
                    <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-800 ring-1 ring-emerald-200 inline-block">
                      {{ $u->defaultCompany->code ?? '' }} {{ $u->defaultCompany->code ? '—' : '' }} {{ $u->defaultCompany->name }}
                    </span>
                  @elseif(!empty($u->default_company_id))
                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 ring-1 ring-slate-200 inline-block">
                      {{ $u->default_company_id }}
                    </span>
                  @endif

                  <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 ring-1 ring-slate-200 inline-block">
                    {{ $u->division->name ?? '-' }}
                  </span>

                  <span class="px-2 py-0.5 rounded-full bg-maroon-100 text-maroon-800 ring-1 ring-maroon-200 inline-block">
                    {{ is_string($u->role ?? null) ? ucfirst($u->role) : (optional($u->role)->name ?? '-') }}
                  </span>

                  @if($u->defaultSite)
                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-maroon-900 ring-1 ring-slate-200 inline-block">
                      {{ $u->defaultSite->code }} — {{ $u->defaultSite->name }}
                    </span>
                  @else
                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 inline-block">No Site</span>
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

          {{-- Modal reset + delete (lanjutkan punyamu di sini) --}}
          {{-- ... --}}
        </div>
      @empty
        <div class="p-10 text-center text-slate-600">Belum ada user.</div>
      @endforelse
    </div>

  </div>

</div>
@endsection
