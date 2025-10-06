{{-- resources/views/admin/sites/create.blade.php --}}
@extends('layouts.app')

@section('title','Tambah Site')

@section('content')
<div class="max-w-3xl mx-auto rounded-2xl overflow-hidden shadow ring-1 ring-slate-200 bg-white">

  <div class="px-6 py-6 bg-gradient-to-r from-emerald-600 via-[--teal] to-[--navy] text-white">
    <h1 class="text-2xl font-bold">🆕 Tambah Site</h1>
    <p class="text-white/80 text-sm mt-1">Daftarkan site baru untuk ERP BISA.</p>
  </div>

  <form action="{{ route('admin.sites.store') }}" method="POST" class="p-6 space-y-4">
    @include('admin.sites._form')

    <div class="flex items-center justify-end gap-2">
      <a href="{{ route('admin.sites.index') }}"
         class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200">Batal</a>
      <button class="px-4 py-2 rounded-xl bg-[--navy] text-white font-semibold hover:bg-[--navy]/90">
        Simpan
      </button>
    </div>
  </form>
</div>
@endsection
