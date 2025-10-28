{{-- resources/views/admin/users/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit User')

@section('header')
  ✏️ Edit User: {{ $user->name }}
@endsection

@section('content')
@php
  use Illuminate\Support\Facades\Storage;

  // Resolver foto/avatar (absolute URL / storage / jetstream / gravatar)
  $avatarUrl = function($u) {
    if (!empty($u->photo_url)) return $u->photo_url;
    if (!empty($u->avatar_path)) return Storage::url($u->avatar_path);
    if (!empty($u->profile_photo_url)) return $u->profile_photo_url;
    $hash = md5(strtolower(trim($u->email ?? '')));
    return "https://www.gravatar.com/avatar/{$hash}?s=240&d=identicon";
  };
@endphp

<div class="bg-white shadow rounded-2xl ring-1 ring-slate-200 overflow-hidden">

  {{-- PASSWORD BANNER (tampil sekali) --}}
  @if (session()->has('generated_password'))
    <div class="px-6 pt-6">
      <div x-data="{open:true, copied:false}" x-show="open" x-transition
           class="relative mb-4 overflow-hidden rounded-2xl bg-white ring-1 ring-[#f6c74d] shadow-[inset_0_0_0_1px_#f6c74d]">
        <div class="bg-gradient-to-r from-maroon-700 via-maroon-600 to-yellow-600 px-4 py-2 text-white text-xs font-semibold tracking-wide">
          🔐 Password Baru — tampil sekali, segera salin
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
            <p class="mt-1 text-xs text-slate-500">Jangan simpan di log. Minta user ganti password setelah login.</p>
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

  <div class="p-6 space-y-8">

    {{-- INFO RINGKAS --}}
    <div class="rounded-2xl ring-1 ring-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
      <div><span class="font-semibold">Nama:</span> {{ $user->name }}</div>
      <div><span class="font-semibold">Email:</span> {{ $user->email }}</div>
      <div class="flex items-center gap-2">
        <span class="font-semibold">Default Site:</span>
        @if($user->defaultSite)
          <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-sky-100 text-sky-700 ring-1 ring-sky-200">
            {{ $user->defaultSite->code }} — {{ $user->defaultSite->name }}
            @if($user->defaultSite->region)
              <span class="ml-1 text-[10px] opacity-80">({{ $user->defaultSite->region }})</span>
            @endif
          </span>
        @else
          <span class="text-slate-500">—</span>
        @endif
      </div>
    </div>

    {{-- ====================== FOTO / AVATAR ====================== --}}
    <div class="grid md:grid-cols-[auto,1fr] gap-5 items-start">
      {{-- Foto saat ini --}}
      <div class="space-y-2">
        <div class="text-sm font-semibold text-slate-700">Foto / Avatar</div>
        <img src="{{ $avatarUrl($user) }}"
             alt="Avatar {{ $user->name }}"
             class="h-28 w-28 rounded-2xl object-cover ring-1 ring-slate-200 shadow-sm bg-slate-100">
        <p class="text-xs text-slate-500">Format: JPG/PNG, maks ±2MB. Disarankan 512×512.</p>

        {{-- Hapus foto (opsional) --}}
        @if(!empty($user->avatar_path) || !empty($user->photo_url) || !empty($user->profile_photo_path))
          <form method="POST" action="{{ route('admin.users.deletePhoto', $user) }}">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="mt-2 px-3 py-1.5 rounded-lg bg-rose-600 text-white text-sm font-semibold hover:bg-rose-500">
              🗑️ Hapus Foto
            </button>
          </form>
        @endif
      </div>

      {{-- Upload / ganti foto --}}
      <div
        x-data="{
          fileName: '',
          previewSrc: '',
          onFileChange(e){
            const f = e.target.files[0];
            if(!f) { this.fileName=''; this.previewSrc=''; return; }
            this.fileName = f.name;
            const reader = new FileReader();
            reader.onload = (ev)=> this.previewSrc = ev.target.result;
            reader.readAsDataURL(f);
          }
        }"
        class="space-y-3"
      >
        <form method="POST" action="{{ route('admin.users.updatePhoto', $user) }}" enctype="multipart/form-data" class="space-y-3">
          @csrf
          @method('PATCH')

          <label class="block text-sm font-semibold text-slate-700">Ganti Foto</label>
          <input type="file" name="photo" accept="image/*" @change="onFileChange"
                 class="block w-full text-sm file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0
                        file:bg-maroon-700 file:text-white hover:file:bg-maroon-800
                        file:font-semibold file:shadow-sm
                        border-slate-300 rounded-lg focus:ring-maroon-600 focus:border-maroon-600" />

          <template x-if="previewSrc">
            <div class="flex items-center gap-3">
              <img :src="previewSrc" alt="Preview"
                   class="h-16 w-16 rounded-xl object-cover ring-1 ring-slate-200 shadow bg-slate-50">
              <div class="text-xs text-slate-600 truncate" x-text="fileName"></div>
            </div>
          </template>

          @error('photo') <p class="text-rose-600 text-sm">{{ $message }}</p> @enderror

          <button type="submit"
                  class="px-4 py-2 rounded-xl bg-maroon-700 text-white font-semibold hover:bg-maroon-600">
            ⤴️ Upload & Simpan
          </button>
        </form>
      </div>
    </div>
    {{-- ==================== /FOTO / AVATAR ==================== --}}

    {{-- UPDATE DIVISION --}}
    <div>
      <form method="POST" action="{{ route('admin.users.updateDivision',$user) }}" class="space-y-2">
        @csrf
        @method('PATCH')

        <label class="block text-sm font-semibold text-slate-700">Division</label>
        <select name="division_id"
                class="mt-1 block w-full rounded-lg border-slate-300 focus:ring-gold-500 focus:border-gold-500">
          <option value="">— None —</option>
          @foreach($divisions as $d)
            <option value="{{ $d->id }}" @selected($user->division_id==$d->id)>{{ $d->name }}</option>
          @endforeach
        </select>
        @error('division_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror

        <button type="submit"
                class="mt-2 px-4 py-2 rounded-xl bg-maroon-700 text-white font-semibold hover:bg-maroon-600">
          Update Division
        </button>
      </form>
    </div>

    {{-- UPDATE DEFAULT SITE --}}
    <div>
      <form method="POST" action="{{ route('admin.users.updateSite',$user) }}" class="space-y-2">
        @csrf
        @method('PATCH')

        <label class="block text-sm font-semibold text-slate-700">Default Site</label>
        <select name="default_site_id"
                class="mt-1 block w-full rounded-lg border-slate-300 focus:ring-gold-500 focus:border-gold-500">
          <option value="">— None —</option>
          @foreach($sites as $s)
            <option value="{{ $s->id }}" @selected($user->default_site_id==$s->id)>
              {{ $s->code }} — {{ $s->name }} @if($s->region) ({{ $s->region }}) @endif
            </option>
          @endforeach
        </select>
        @error('default_site_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror

        <button type="submit"
                class="mt-2 px-4 py-2 rounded-xl bg-sky-600 text-white font-semibold hover:bg-sky-500">
          Update Default Site
        </button>
      </form>
    </div>

    {{-- RESET PASSWORD (generate random) --}}
    <div class="pt-2">
      <form method="POST" action="{{ route('admin.users.resetPassword',$user) }}" class="space-y-2">
        @csrf
        <p class="text-sm text-slate-600">
          Menekan tombol ini akan <span class="font-semibold text-slate-800">membuat password acak baru</span>
          dan menampilkannya sekali di atas.
        </p>
        <button type="submit"
                class="px-4 py-2 rounded-xl bg-emerald-600 text-white font-semibold hover:bg-emerald-500">
          🔐 Reset Password (Generate)
        </button>
      </form>
    </div>

  </div>
</div>
@endsection
