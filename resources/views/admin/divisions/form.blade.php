@extends('layouts.app')

@section('title', $division->exists ? 'Edit Divisi' : 'Tambah Divisi')

@section('header')
  <h1 class="text-xl font-bold text-maroon-800">
    {{ $division->exists ? '✏️ Edit Divisi' : '➕ Tambah Divisi' }}
  </h1>
@endsection

@section('content')
<div class="bg-white shadow rounded-xl p-6 space-y-6 max-w-xl">
  <form method="POST"
        action="{{ $division->exists ? route('admin.divisions.update',$division) : route('admin.divisions.store') }}">
    @csrf
    @if($division->exists) @method('PUT') @endif

    <div class="space-y-2">
      <label class="block text-sm font-medium text-gray-700">Nama Divisi</label>
      <input type="text" name="name" value="{{ old('name',$division->name) }}" required
             class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-gold-500 focus:border-gold-500">
      @error('name') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>

    <div class="space-y-2">
      <label class="block text-sm font-medium text-gray-700">Kode</label>
      <input type="text" name="code" value="{{ old('code',$division->code) }}" required
             class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-gold-500 focus:border-gold-500">
      @error('code') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>

    <div class="space-y-2">
      <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
      <textarea name="description" rows="3"
                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-gold-500 focus:border-gold-500">{{ old('description',$division->description) }}</textarea>
      @error('description') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>

    <div class="flex items-center gap-2">
      <input type="checkbox" name="is_active" value="1" {{ old('is_active',$division->is_active) ? 'checked' : '' }}>
      <label class="text-sm font-medium text-gray-700">Aktif</label>
    </div>

    <div class="flex gap-3 pt-4">
      <a href="{{ route('admin.divisions.index') }}"
         class="px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300">⬅ Kembali</a>
      <button type="submit"
              class="px-4 py-2 rounded-lg bg-gold-500 text-maroon-900 font-semibold hover:bg-gold-400">
        💾 Simpan
      </button>
    </div>
  </form>
</div>
@endsection
