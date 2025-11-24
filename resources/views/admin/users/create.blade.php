{{-- resources/views/admin/users/create.blade.php --}}
@extends('layouts.app')

@section('title','Tambah User')

@section('content')
@php
  use Illuminate\Support\Arr;

  $companies = $companies ?? collect();
  $divisions = $divisions ?? collect();
  $sites     = $sites ?? collect();

  // preselect company: old > query/session dari controller
  $selectedCompanyId = old('default_company_id') ?: ($companyId ?? null);

  $oldSiteIds = old('site_ids', []);
  $oldSiteIds = Arr::wrap($oldSiteIds);
@endphp

<div class="max-w-3xl mx-auto rounded-3xl overflow-hidden shadow ring-1 ring-slate-200 bg-white">

  {{-- HEADER --}}
  <div class="px-6 py-7 text-white relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-r from-maroon-800 via-maroon-700 to-maroon-600"></div>
    <div class="absolute inset-0 opacity-25 bg-[radial-gradient(70%_70%_at_10%_10%,_rgba(255,255,255,0.5)_0%,_transparent_60%)]"></div>
    <div class="absolute -top-16 -right-16 size-64 rounded-full bg-white/10 blur-3xl"></div>

    <div class="relative">
      <h1 class="text-2xl font-bold tracking-tight">➕ Tambah User</h1>
      <p class="text-sm text-white/85 mt-1">Buat akun user baru untuk ERP BISA.</p>
    </div>
  </div>

  {{-- FORM --}}
  <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
    @csrf

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
        <template x-if="previewSrc">
          <img :src="previewSrc" alt="Preview"
               class="h-16 w-16 rounded-xl object-cover ring-1 ring-slate-200 shadow bg-white">
        </template>
        <template x-if="!previewSrc">
          <div class="h-16 w-16 rounded-xl grid place-items-center ring-1 ring-slate-200 bg-white text-slate-400">
            <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path d="M4 17l6-6 4 4 5-5" stroke-width="2"/>
              <rect x="2" y="4" width="20" height="16" rx="3" ry="3" stroke-width="2"/>
            </svg>
          </div>
        </template>
        <div class="min-w-0">
          <input type="file" name="photo" accept="image/*" @change="onFileChange"
                 class="block w-full text-sm file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0
                        file:bg-maroon-700 file:text-white hover:file:bg-maroon-800 file:font-semibold file:shadow-sm
                        border border-slate-300 rounded-lg focus:ring-maroon-700 focus:border-maroon-700" />
          <div class="text-xs text-slate-500 mt-1" x-text="fileName || 'Format: JPG/PNG, maks ±2MB. Disarankan 512×512.'"></div>
          @error('photo') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>
      </div>
    </div>

    <div class="grid sm:grid-cols-2 gap-4">

      <div>
        <label class="block text-sm font-semibold text-slate-700">Nama</label>
        <input type="text" name="name" value="{{ old('name') }}" required
               class="mt-1 block w-full rounded-xl border-slate-300 focus:ring-maroon-700 focus:border-maroon-700">
        @error('name') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-semibold text-slate-700">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required
               class="mt-1 block w-full rounded-xl border-slate-300 focus:ring-maroon-700 focus:border-maroon-700">
        @error('email') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- ✅ Default Company (auto reload to filter divisions/sites) --}}
      <div class="sm:col-span-2"
           x-data="{
             cid: '{{ $selectedCompanyId }}',
             changeCompany(){
               const url = new URL(window.location.href);
               if(this.cid){
                 url.searchParams.set('company_id', this.cid);
               } else {
                 url.searchParams.delete('company_id');
               }
               window.location.href = url.toString();
             }
           }"
      >
        <label class="block text-sm font-semibold text-slate-700">Perusahaan (Default)</label>
        <select name="default_company_id"
                x-model="cid"
                @change="changeCompany()"
                class="mt-1 block w-full rounded-xl border-slate-300 focus:ring-maroon-700 focus:border-maroon-700">
          <option value="">— Pilih Perusahaan —</option>
          @foreach($companies as $c)
            <option value="{{ $c->id }}">
              {{ $c->code ?? 'COMP' }} — {{ $c->name }}
            </option>
          @endforeach
        </select>
        <p class="text-xs text-slate-500 mt-1">
          Kalau kamu ganti perusahaan, halaman bakal reload biar Divisi & Site otomatis ngikut company itu.
        </p>
        @error('default_company_id') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-semibold text-slate-700">Divisi</label>
        <select name="division_id"
                class="mt-1 block w-full rounded-xl border-slate-300 focus:ring-maroon-700 focus:border-maroon-700">
          <option value="">— Pilih Divisi —</option>
          @foreach($divisions as $d)
            <option value="{{ $d->id }}" @selected(old('division_id') == $d->id)>
              {{ $d->name }}
            </option>
          @endforeach
        </select>
        @error('division_id') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-semibold text-slate-700">Role</label>
        <select name="role" required
                class="mt-1 block w-full rounded-xl border-slate-300 focus:ring-maroon-700 focus:border-maroon-700">
          <option value="">— Pilih Role —</option>
          <option value="super_admin" @selected(old('role')==='super_admin')>Super Admin</option>
          <option value="gm"          @selected(old('role')==='gm')>General Manager</option>
          <option value="manager"     @selected(old('role')==='manager')>Manager</option>
          <option value="staff"       @selected(old('role')==='staff')>Staff</option>
        </select>
        @error('role') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- Default Site (single) --}}
      <div class="sm:col-span-2">
        <label class="block text-sm font-semibold text-slate-700">Default Site</label>
        <select name="default_site_id"
                class="mt-1 block w-full rounded-xl border-slate-300 focus:ring-maroon-700 focus:border-maroon-700">
          <option value="">— Tanpa Default Site —</option>
          @foreach($sites as $s)
            <option value="{{ $s->id }}" @selected(old('default_site_id') == $s->id)>
              {{ $s->code }} — {{ $s->name }}
            </option>
          @endforeach
        </select>
        <p class="text-xs text-slate-500 mt-1">
          Non-GM/non-Super Admin akan terkunci ke default site ini saat login.
        </p>
        @error('default_site_id') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- Allowed Sites (multi / array) --}}
      <div class="sm:col-span-2">
        <label class="block text-sm font-semibold text-slate-700">
          Site yang Diizinkan (multi / array)
        </label>

        <div class="mt-2 grid sm:grid-cols-2 gap-2 max-h-64 overflow-y-auto pr-1">
          @foreach($sites as $s)
            <label class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 hover:bg-slate-50">
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
      <label class="block text-sm font-semibold text-slate-700 mb-1">Password (opsional)</label>
      <input type="password" name="password" placeholder="Biarkan kosong untuk generate otomatis"
             class="block w-full rounded-xl border-slate-300 focus:ring-maroon-700 focus:border-maroon-700">
      @error('password') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
      <p class="text-xs text-slate-500 mt-1">
        Jika dikosongkan, sistem akan membuat password acak dan menampilkannya sekali.
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
        💾 Simpan
      </button>
    </div>

  </form>
</div>
@endsection
