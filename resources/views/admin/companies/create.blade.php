@extends('layouts.app')
@section('title', 'Create Company')

@section('content')
<div class="max-w-4xl mx-auto space-y-5">

  {{-- Header --}}
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold text-slate-900">Create Company</h1>
      <p class="text-sm text-slate-500 mt-1">Tambah perusahaan baru untuk ARCA.</p>
    </div>

    <a href="{{ route('admin.companies.index') }}"
       class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200">
      ← Back
    </a>
  </div>

  {{-- Errors --}}
  @if ($errors->any())
    <div class="p-4 rounded-2xl bg-rose-50 text-rose-800 ring-1 ring-rose-200">
      <div class="font-bold mb-2">Form masih ada error:</div>
      <ul class="list-disc ml-5 space-y-1 text-sm">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- Form --}}
  <form action="{{ route('admin.companies.store') }}" method="POST"
        class="rounded-2xl bg-white ring-1 ring-slate-200 shadow-sm p-5 space-y-4">
    @csrf

    <div>
      <label class="text-sm font-semibold text-slate-700">Code</label>
      <input type="text" name="code" value="{{ old('code') }}"
             placeholder="contoh: ABN, AAP, DIPSOL"
             class="mt-1 w-full rounded-xl border-slate-300 focus:border-maroon-600 focus:ring-maroon-600/30">
      <div class="text-xs text-slate-500 mt-1">
        Unik, tanpa spasi. Boleh pakai dash/underscore.
      </div>
    </div>

    <div>
      <label class="text-sm font-semibold text-slate-700">Name</label>
      <input type="text" name="name" value="{{ old('name') }}"
             placeholder="Nama perusahaan"
             class="mt-1 w-full rounded-xl border-slate-300 focus:border-maroon-600 focus:ring-maroon-600/30">
    </div>

    <div>
      <label class="text-sm font-semibold text-slate-700">Description (opsional)</label>
      <textarea name="description" rows="3"
                placeholder="Penjelasan singkat perusahaan"
                class="mt-1 w-full rounded-xl border-slate-300 focus:border-maroon-600 focus:ring-maroon-600/30">{{ old('description') }}</textarea>
    </div>

    <div class="flex items-center gap-2">
      <input id="is_active" type="checkbox" name="is_active" value="1"
             class="rounded border-slate-300 text-maroon-700 focus:ring-maroon-600/30"
             {{ old('is_active', '1') ? 'checked' : '' }}>
      <label for="is_active" class="text-sm text-slate-700 font-semibold">
        Active
      </label>
    </div>

    <div class="flex items-center justify-end gap-2 pt-2">
      <a href="{{ route('admin.companies.index') }}"
         class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200">
        Cancel
      </a>
      <button type="submit"
              class="px-5 py-2 rounded-xl bg-maroon-700 hover:bg-maroon-800 text-white text-sm font-bold shadow">
        Save Company
      </button>
    </div>
  </form>
</div>
@endsection
