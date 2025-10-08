{{-- resources/views/admin/sites/edit.blade.php --}}
@extends('layouts.app')

@section('title','Edit Site')

@section('content')
<div class="max-w-3xl mx-auto rounded-3xl overflow-hidden shadow ring-1 ring-slate-200 bg-white">

  {{-- HEADER (maroon-only • konsisten ARCA) --}}
  <div class="px-6 py-7 text-white relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-r from-maroon-800 via-maroon-700 to-maroon-600"></div>
    <div class="absolute inset-0 opacity-25 bg-[radial-gradient(70%_70%_at_10%_10%,_rgba(255,255,255,0.5)_0%,_transparent_60%)]"></div>
    <div class="absolute -top-16 -right-16 size-64 rounded-full bg-white/10 blur-3xl"></div>

    <div class="relative">
      <h1 class="text-2xl font-bold tracking-tight">ARCA</h1>
      <p class="text-white/85 text-sm mt-1">✏️ Perbarui informasi Site</p>
    </div>
  </div>

  {{-- FORM UPDATE --}}
  <form action="{{ route('admin.sites.update', $site) }}" method="POST" class="p-6 space-y-5">
    @csrf
    @method('PUT')

    @include('admin.sites._form', ['site' => $site])

    <div class="flex items-center justify-between gap-2 pt-4">
      {{-- Tombol HAPUS: submit ke form terpisah (hidden) --}}
      <button type="submit"
              form="delete-site-{{ $site->getKey() }}"
              formnovalidate
              onclick="return confirm('Pindahkan site ini ke trash?')"
              class="px-4 py-2 rounded-xl bg-gradient-to-r from-rose-600 to-red-600 text-white font-semibold shadow hover:brightness-110">
        🗑 Hapus
      </button>

      <div class="flex items-center gap-2">
        <a href="{{ route('admin.sites.index') }}"
           class="px-4 py-2 rounded-xl text-sm font-medium text-slate-700 ring-1 ring-slate-200 hover:bg-slate-50">
          ← Kembali
        </a>
        <button type="submit"
                class="px-4 py-2 rounded-xl bg-maroon-700 text-white font-semibold hover:bg-maroon-800 ring-1 ring-maroon-900/20">
          Update
        </button>
      </div>
    </div>
  </form>

  {{-- FORM DELETE (dipisah, hidden) --}}
  <form id="delete-site-{{ $site->getKey() }}"
        action="{{ route('admin.sites.destroy', $site) }}"
        method="POST" class="hidden">
    @csrf
    @method('DELETE')
  </form>
</div>
@endsection
