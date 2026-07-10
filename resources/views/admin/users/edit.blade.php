{{-- resources/views/admin/users/edit.blade.php --}}
@extends('layouts.app')

@section('title','Edit User')

@section('content')
@php
  use Illuminate\Support\Facades\Storage;
  use Illuminate\Support\Arr;

  $companies = $companies ?? collect();
  $divisions = $divisions ?? collect();
  $sites     = $sites ?? collect();

  // old values / current
  $oldSiteIds = old('site_ids',
      $user->sites?->pluck('id')->all()
      ?? (array)($user->allowed_site_ids ?? [])
  );
  $oldSiteIds = Arr::wrap($oldSiteIds);

  // Avatar resolver
  $avatarUrl = function($u) {
    if (!empty($u->photo_url)) return $u->photo_url;
    if (!empty($u->avatar_path)) return Storage::url($u->avatar_path);
    if (!empty($u->profile_photo_url)) return $u->profile_photo_url;
    $hash = md5(strtolower(trim($u->email ?? '')));
    return "https://www.gravatar.com/avatar/{$hash}?s=160&d=identicon";
  };

  $statusMsg = session('status') ?? session('message');
@endphp

<div class="max-w-3xl mx-auto rounded-lg shadow ring-1 ring-slate-200 bg-white">

  {{-- HEADER (maroon konsisten ARCA) --}}
  <div class="px-6 py-7 text-white relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-r from-maroon-800 via-maroon-700 to-maroon-600"></div>
    <div class="absolute inset-0 opacity-25 bg-[radial-gradient(70%_70%_at_10%_10%,_rgba(255,255,255,0.5)_0%,_transparent_60%)]"></div>
    <div class="absolute -top-16 -right-16 size-64 rounded-full bg-white/10 blur-3xl"></div>

    <div class="relative">
      <h1 class="text-2xl font-bold tracking-tight"> Edit User</h1>
      <p class="text-sm text-white/85 mt-1">{{ $user->name }} — {{ $user->email }}</p>
    </div>
  </div>

  {{-- STATUS --}}
  @if($statusMsg)
    <div class="px-6 pt-5">
      <div class="mb-3 rounded-2xl border border-maroon-200 bg-maroon-50/70 ring-1 ring-maroon-100 shadow-sm px-4 py-3 text-sm text-maroon-900">
        {{ $statusMsg }}
      </div>
    </div>
  @endif

  {{-- PASSWORD BANNER (tampil sekali) --}}
  @if (session()->has('generated_password'))
    <div class="px-6 pt-2">
      <div x-data="{open:true, copied:false}" x-show="open" x-transition
           class="relative mb-4 overflow-hidden rounded-2xl bg-white ring-1 ring-[#BD9B75] shadow-[inset_0_0_0_1px_#BD9B75]">
        <div class="bg-gradient-to-r from-maroon-700 via-maroon-600 to-yellow-600 px-4 py-2 text-white text-xs font-semibold tracking-wide">
           Password Baru — tampil sekali, segera salin
        </div>
        <div class="p-4 flex items-start gap-3">
          <svg class="h-5 w-5 text-yellow-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="2" d="M12 9v4m0 4h.01" />
            <path stroke-width="2" d="M12 2a10 10 0 100 20 10 10 0 000-20z" />
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
                  <rect x="9" y="9" width="13" height="13" rx="2" />
                  <rect x="2" y="2" width="13" height="13" rx="2" />
                </svg>
                <span x-show="!copied">Copy</span>
                <span x-show="copied" class="text-emerald-700">Copied!</span>
              </button>
            </div>
            <p class="mt-1 text-xs text-slate-500">Minta user ganti password setelah login.</p>
          </div>
          <button @click="open=false" class="ml-auto rounded-lg p-1.5 text-slate-600 hover:bg-slate-100">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path d="M18 6 6 18M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  @endif

  {{-- FORM UTAMA --}}
  <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
    @csrf
    @method('PATCH')

    {{-- Foto / Avatar --}}
    <div
      x-data="{
        fileName: '',
        previewSrc: '',
        onFileChange(e){
          const f = e.target.files[0];
          if(!f){ this.fileName=''; this.previewSrc=''; return; }
          this.fileName = f.name;
          const reader = new FileReader();
          reader.onload = (ev)=> this.previewSrc = ev.target.result;
          reader.readAsDataURL(f);
        }
      }"
      class="rounded-2xl ring-1 ring-slate-200 p-4 bg-slate-50"
    >
      <label class="block text-sm font-semibold text-slate-700 mb-2">Foto / Avatar (opsional)</label>
      <div class="flex items-center gap-4">
        <img src="{{ $avatarUrl($user) }}" alt="Current Avatar"
             class="h-16 w-16 rounded-xl object-cover ring-1 ring-slate-200 shadow bg-white">

        <template x-if="previewSrc">
          <img :src="previewSrc" alt="Preview"
               class="h-16 w-16 rounded-xl object-cover ring-1 ring-slate-200 shadow bg-white">
        </template>

        <div class="min-w-0 flex-1">
          <input type="file" name="photo" accept="image/*" @change="onFileChange"
                 class="block w-full text-sm file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0
                        file:bg-maroon-700 file:text-white hover:file:bg-maroon-800 file:font-semibold file:shadow-sm
                        border border-slate-300 rounded-lg focus:ring-maroon-700 focus:border-maroon-700" />
          <div class="text-xs text-slate-500 mt-1"
               x-text="fileName || 'Format: JPG/PNG, maks ±2MB. Disarankan 512×512.'"></div>
          @error('photo') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- delete photo --}}
        @if(!empty($user->avatar_path) || !empty($user->photo_url) || !empty($user->profile_photo_path))
          <form method="POST" action="{{ route('admin.users.deletePhoto', $user) }}">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="px-3 py-2 rounded-xl bg-rose-600 text-white text-xs font-semibold hover:bg-rose-500">
               Hapus
            </button>
          </form>
        @endif
      </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-4">

      <div>
        <label class="block text-sm font-semibold text-slate-700">Nama</label>
        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
               class="mt-1 block w-full rounded-lg border border-slate-300 focus:ring-maroon-700 focus:border-maroon-700">
        @error('name') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-semibold text-slate-700">Email</label>
        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
               class="mt-1 block w-full rounded-lg border border-slate-300 focus:ring-maroon-700 focus:border-maroon-700">
        @error('email') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- Default Company --}}
      <div>
        <label class="block text-sm font-semibold text-slate-700">Perusahaan (Default)</label>
        <select name="default_company_id"
                class="mt-1 block w-full rounded-lg border border-slate-300 focus:ring-maroon-700 focus:border-maroon-700">
          <option value="">— Pilih Perusahaan —</option>
          @foreach($companies as $c)
            <option value="{{ $c->id }}" @selected(old('default_company_id', $user->default_company_id) == $c->id)>
              {{ $c->code ?? 'COMP' }} — {{ $c->name }}
            </option>
          @endforeach
        </select>
        @error('default_company_id') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- Division --}}
      <div>
        <label class="block text-sm font-semibold text-slate-700">Divisi</label>
        <select name="division_id"
                class="mt-1 block w-full rounded-lg border border-slate-300 focus:ring-maroon-700 focus:border-maroon-700">
          <option value="">— Pilih Divisi —</option>
          @foreach($divisions as $d)
            <option value="{{ $d->id }}" @selected(old('division_id',$user->division_id) == $d->id)>
              {{ $d->name }}
            </option>
          @endforeach
        </select>
        @error('division_id') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- Role --}}
      <div>
        <label class="block text-sm font-semibold text-slate-700">Role</label>
        <select name="role" required
                class="mt-1 block w-full rounded-lg border border-slate-300 focus:ring-maroon-700 focus:border-maroon-700">
          <option value="super_admin" @selected(old('role',$user->role)==='super_admin')>Super Admin</option>
          <option value="gm"          @selected(old('role',$user->role)==='gm')>General Manager</option>
          <option value="manager"     @selected(old('role',$user->role)==='manager')>Manager</option>
          <option value="staff"       @selected(old('role',$user->role)==='staff')>Staff</option>
          <option value="creator"     @selected(old('role',$user->role)==='creator')>Creator</option>
        </select>
        @error('role') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- Default Site --}}
      <div class="sm:col-span-2">
        <label class="block text-sm font-semibold text-slate-700">Default Site</label>
        <select name="default_site_id"
                class="mt-1 block w-full rounded-lg border border-slate-300 focus:ring-maroon-700 focus:border-maroon-700">
          <option value="">— Tanpa Default Site —</option>
          @foreach($sites as $s)
            <option value="{{ $s->id }}" @selected(old('default_site_id',$user->default_site_id) == $s->id)>
              {{ $s->code }} — {{ $s->name }}
            </option>
          @endforeach
        </select>
        <p class="text-xs text-slate-500 mt-1">
          Non-GM/non-Super Admin akan terkunci ke default site ini saat login.
        </p>
        @error('default_site_id') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- Allowed Sites (multi) --}}
      <div class="sm:col-span-2">
        <label class="block text-sm font-semibold text-slate-700">
          Site yang Diizinkan (multi / array)
        </label>

        <div class="mt-2 grid sm:grid-cols-2 gap-2 max-h-64 overflow-y-auto pr-1">
          @foreach($sites as $s)
            <label class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 hover:bg-slate-50">
              <input type="checkbox"
                     name="site_ids[]"
                     value="{{ $s->id }}"
                     class="rounded border-slate-300 text-maroon-700 focus:ring-maroon-600"
                     @checked(in_array($s->id, $oldSiteIds, true))>
              <span class="text-sm text-slate-800">
                <span class="font-semibold">{{ $s->code }}</span>
                <span class="text-slate-500">— {{ $s->name }}</span>
                @if(!empty($s->region))
                  <span class="text-[11px] text-slate-400 ml-1">({{ $s->region }})</span>
                @endif
              </span>
            </label>
          @endforeach
        </div>

        <p class="text-xs text-slate-500 mt-1">
          Ini akses tambahan. Default Site tetap jadi site utama di session pertama.
        </p>

        @error('site_ids') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
        @error('site_ids.*') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
      </div>
    </div>

    {{-- Password manual (opsional) --}}
    <div class="rounded-2xl ring-1 ring-slate-200 p-4 bg-slate-50">
      <label class="block text-sm font-semibold text-slate-700 mb-1">Password Baru (opsional)</label>
      <input type="password" name="password" placeholder="Kosongkan jika tidak ingin mengganti"
             class="block w-full rounded-xl border border-slate-300 focus:ring-maroon-700 focus:border-maroon-700">
      @error('password') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
      <p class="text-xs text-slate-500 mt-1">
        Kalau dikosongkan, password tidak berubah.
      </p>
    </div>

    {{-- Actions --}}
    <div class="pt-2 flex items-center justify-between gap-3">
      <a href="{{ route('admin.users.index') }}"
         class="px-4 py-2 rounded-xl text-sm font-medium text-slate-700 ring-1 ring-slate-200 hover:bg-slate-50">
        ← Kembali
      </a>
      <button type="submit"
              class="px-4 py-2 rounded-xl bg-maroon-700 text-white font-semibold hover:bg-maroon-800 ring-1 ring-maroon-900/20">
         Simpan Perubahan
      </button>
    </div>
  </form>

  {{-- RESET PASSWORD BUTTON --}}
  <div class="px-6 pb-6">
    <form method="POST" action="{{ route('admin.users.resetPassword',$user) }}" class="mt-4">
      @csrf
      <button type="submit"
              class="w-full px-4 py-2 rounded-xl bg-emerald-600 text-white font-semibold hover:bg-emerald-500">
         Reset Password (Generate)
      </button>
    </form>
  </div>

</div>
@endsection
