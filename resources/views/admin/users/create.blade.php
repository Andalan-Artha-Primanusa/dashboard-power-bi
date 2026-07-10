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

<div class="max-w-5xl mx-auto space-y-6">

  {{-- HEADER MAROON (seragam ARCA) --}}
  <div class="relative overflow-hidden rounded-3xl ring-1 ring-white/40 bg-gradient-to-r from-maroon-800 via-maroon-700 to-maroon-600">
    <div class="absolute inset-0 opacity-25 bg-[radial-gradient(70%_70%_at_10%_10%,_rgba(255,255,255,0.5)_0%,_transparent_60%)]"></div>
    <div class="absolute -top-16 -right-16 size-64 rounded-full bg-white/10 blur-3xl"></div>

    <div class="relative px-6 py-6 sm:px-8 sm:py-7 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 text-white">
      <div class="space-y-1">
        <div class="inline-flex items-center gap-2 text-[11px] font-semibold text-white/85 uppercase tracking-wide">
          <span class="inline-flex h-6 w-6 items-center justify-center rounded-xl bg-white/20 ring-1 ring-white/30">
            <img src="{{ asset('assets/images/logoarca.png') }}" alt="ARCA" class="h-4 w-4 object-contain">
          </span>
          <span>ARCA — User Management</span>
        </div>
        <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight">
           Tambah User
        </h1>
        <p class="text-xs sm:text-sm text-white/80 max-w-xl">
          Buat akun user baru untuk ERP BISA dan modul ARCA lainnya.
        </p>
      </div>

      <div class="flex flex-wrap gap-2 justify-start sm:justify-end">
        <a href="{{ route('admin.users.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 text-white text-sm font-semibold hover:bg-white/20 ring-1 ring-white/40">
          <span class="text-lg leading-none">←</span>
          <span>Kembali ke daftar</span>
        </a>
      </div>
    </div>
  </div>

  {{-- ALERT ERROR GLOBAL --}}
  @if ($errors->any())
    <div class="relative rounded-2xl border border-rose-200 bg-rose-50/90 ring-1 ring-rose-100 shadow-sm">
      <div class="flex items-start gap-3 p-4">
        <svg class="h-5 w-5 text-rose-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-width="2" d="M12 9v4m0 4h.01"/>
          <path stroke-width="2" d="M12 2a10 10 0 100 20 10 10 0 000-20z"/>
        </svg>
        <div class="text-sm text-rose-900">
          <div class="font-semibold mb-1">Form masih ada error:</div>
          <ul class="list-disc ml-5 space-y-0.5">
            @foreach ($errors->all() as $e)
              <li>{{ $e }}</li>
            @endforeach
          </ul>
        </div>
      </div>
      <div class="h-1 w-full bg-gradient-to-r from-rose-500 via-rose-400 to-rose-600"></div>
    </div>
  @endif

  {{-- FORM CARD --}}
  <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data"
        class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm ring-1 ring-slate-100/80">
    @csrf

    {{-- accent bar kiri --}}
    <div class="absolute inset-y-0 left-0 w-1.5 bg-gradient-to-b from-maroon-700 via-maroon-600 to-maroon-700"></div>

    <div class="px-6 py-6 sm:px-8 sm:py-7 space-y-6">

      {{-- header kecil --}}
      <div class="flex items-center justify-between gap-3 pb-3 border-b border-slate-100">
        <div>
          <h2 class="text-sm font-semibold tracking-wide text-slate-700 uppercase">
            Detail User
          </h2>
          <p class="text-xs text-slate-500 mt-0.5">
            Isi data dasar, perusahaan, role, dan site untuk user baru.
          </p>
        </div>
      </div>

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
        class="rounded-2xl border border-slate-200 p-4 bg-slate-50/60"
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
                   class="block w-full text-sm
                          file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0
                          file:bg-maroon-700 file:text-white hover:file:bg-maroon-800 file:font-semibold file:shadow-sm
                          border border-slate-300 rounded-lg focus:ring-maroon-700 focus:border-maroon-700" />
            <div class="text-xs text-slate-500 mt-1"
                 x-text="fileName || 'Format: JPG/PNG, maks ±2MB. Disarankan 512×512.'"></div>
            @error('photo') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
          </div>
        </div>
      </div>

      <div class="grid sm:grid-cols-2 gap-4">
        {{-- Nama --}}
        <div>
          <label class="block text-sm font-semibold text-slate-700">Nama</label>
          <input type="text" name="name" value="{{ old('name') }}" required
                 class="mt-1 block w-full rounded-2xl border border-sky-200 bg-white
                        px-4 py-2.5 text-sm text-slate-700 shadow-sm
                        focus:outline-none focus:ring-2 focus:ring-sky-300/70 focus:border-sky-400">
          @error('name') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Email --}}
        <div>
          <label class="block text-sm font-semibold text-slate-700">Email</label>
          <input type="email" name="email" value="{{ old('email') }}" required
                 class="mt-1 block w-full rounded-2xl border border-sky-200 bg-white
                        px-4 py-2.5 text-sm text-slate-700 shadow-sm
                        focus:outline-none focus:ring-2 focus:ring-sky-300/70 focus:border-sky-400">
          @error('email') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Default Company (auto reload) --}}
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
                  class="mt-1 block w-full rounded-2xl border border-sky-200 bg-white
                         px-3 py-2.5 text-sm text-slate-700 shadow-sm
                         focus:outline-none focus:ring-2 focus:ring-sky-300/70 focus:border-sky-400">
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

        {{-- Divisi --}}
        <div>
          <label class="block text-sm font-semibold text-slate-700">Divisi</label>
          <select name="division_id"
                  class="mt-1 block w-full rounded-2xl border border-sky-200 bg-white
                         px-3 py-2.5 text-sm text-slate-700 shadow-sm
                         focus:outline-none focus:ring-2 focus:ring-sky-300/70 focus:border-sky-400">
            <option value="">— Pilih Divisi —</option>
            @foreach($divisions as $d)
              <option value="{{ $d->id }}" @selected(old('division_id') == $d->id)>
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
                  class="mt-1 block w-full rounded-2xl border border-sky-200 bg-white
                         px-3 py-2.5 text-sm text-slate-700 shadow-sm
                         focus:outline-none focus:ring-2 focus:ring-sky-300/70 focus:border-sky-400">
            <option value="">— Pilih Role —</option>
            <option value="super_admin" @selected(old('role')==='super_admin')>Super Admin</option>
            <option value="gm"          @selected(old('role')==='gm')>General Manager</option>
            <option value="manager"     @selected(old('role')==='manager')>Manager</option>
            <option value="staff"       @selected(old('role')==='staff')>Staff</option>
            <option value="creator"    @selected(old('role')==='creator')>Creator</option>
          </select>
          @error('role') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Default Site --}}
        <div class="sm:col-span-2">
          <label class="block text-sm font-semibold text-slate-700">Default Site</label>
          <select name="default_site_id"
                  class="mt-1 block w-full rounded-2xl border border-sky-200 bg-white
                         px-3 py-2.5 text-sm text-slate-700 shadow-sm
                         focus:outline-none focus:ring-2 focus:ring-sky-300/70 focus:border-sky-400">
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

        {{-- Allowed Sites --}}
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
      <div class="rounded-2xl border border-slate-200 p-4 bg-slate-50/60">
        <label class="block text-sm font-semibold text-slate-700 mb-1">Password (opsional)</label>
        <input type="password" name="password" placeholder="Biarkan kosong untuk generate otomatis"
               class="block w-full rounded-2xl border border-sky-200 bg-white
                      px-4 py-2.5 text-sm text-slate-700 shadow-sm
                      focus:outline-none focus:ring-2 focus:ring-sky-300/70 focus:border-sky-400">
        @error('password') <p class="text-sm text-rose-600 mt-1">{{ $message }}</p> @enderror
        <p class="text-xs text-slate-500 mt-1">
          Jika dikosongkan, sistem akan membuat password acak dan menampilkannya sekali.
        </p>
      </div>

      {{-- Actions --}}
      <div class="pt-2 flex items-center justify-between gap-3 border-t border-slate-100">
        <a href="{{ route('admin.users.index') }}"
           class="px-4 py-2 rounded-xl text-sm font-medium text-slate-700 ring-1 ring-slate-200 bg-white hover:bg-slate-50">
          ← Kembali
        </a>
        <button type="submit"
                class="px-4 py-2 rounded-xl bg-maroon-700 text-white text-sm font-semibold hover:bg-maroon-800 shadow ring-1 ring-maroon-900/20">
           Simpan
        </button>
      </div>

    </div>
  </form>
</div>
@endsection
