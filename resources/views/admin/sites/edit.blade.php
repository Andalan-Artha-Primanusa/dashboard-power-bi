{{-- resources/views/admin/sites/edit.blade.php --}}
@extends('layouts.app')

@section('title','Edit Site')

@section('content')
<div class="max-w-3xl mx-auto rounded-2xl overflow-hidden shadow ring-1 ring-slate-200 bg-white">

  <div class="px-6 py-6 bg-gradient-to-r from-emerald-600 via-[--teal] to-[--navy] text-white">
    <h1 class="text-2xl font-bold">✏️ Edit Site</h1>
    <p class="text-white/80 text-sm mt-1">Perbarui informasi site.</p>
  </div>

  <form action="{{ route('admin.sites.update', $site) }}" method="POST" class="p-6 space-y-4">
    @method('PUT')
    @include('admin.sites._form', ['site' => $site])

    <div class="flex items-center justify-between gap-2">
      <form action="{{ route('admin.sites.destroy', $site) }}" method="POST" onsubmit="return confirm('Pindahkan ke trash?')">
        @csrf @method('DELETE')
        <button class="px-4 py-2 rounded-xl bg-red-600 text-white hover:bg-red-700">Hapus</button>
      </form>

      <div class="flex items-center gap-2">
        <a href="{{ route('admin.sites.index') }}"
           class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200">Kembali</a>
        <button class="px-4 py-2 rounded-xl bg-[--navy] text-white font-semibold hover:bg-[--navy]/90">
          Update
        </button>
      </div>
    </div>
  </form>
</div>
@endsection
