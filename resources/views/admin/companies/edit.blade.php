{{-- resources/views/admin/companies/edit.blade.php --}}
@extends('layouts.app')
@section('title', 'Edit Company')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

  {{-- HEADER MAROON (seragam ARCA) --}}
  <div class="relative overflow-hidden rounded-3xl ring-1 ring-white/40 bg-gradient-to-r from-maroon-800 via-maroon-700 to-maroon-600">
    <div class="absolute inset-0 opacity-25 bg-[radial-gradient(90%_70%_at_0%_0%,_rgba(255,255,255,0.55),_transparent_60%)]"></div>
    <div class="absolute -top-16 -right-16 size-64 rounded-full bg-white/10 blur-3xl"></div>

    <div class="relative px-6 py-6 sm:px-8 sm:py-7 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 text-white">
      <div class="space-y-1">
        <div class="inline-flex items-center gap-2 text-[11px] font-semibold text-white/85 uppercase tracking-wide">
          <span class="inline-flex h-6 w-6 items-center justify-center rounded-xl bg-white/20 ring-1 ring-white/30">
            <img src="{{ asset('assets/images/logoarca.png') }}" alt="ARCA" class="h-4 w-4 object-contain">
          </span>
          <span>ARCA — Master Data</span>
        </div>
        <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight">
          🏢 Edit Company
        </h1>
        <p class="text-xs sm:text-sm text-white/80 max-w-xl">
          Update data perusahaan yang sudah terdaftar di ARCA.
        </p>
      </div>

      <div class="flex flex-wrap gap-2 justify-start sm:justify-end">
        <a href="{{ route('admin.companies.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 text-white text-sm font-semibold hover:bg-white/20 ring-1 ring-white/40">
          <span class="text-lg leading-none">←</span>
          <span>Back to Companies</span>
        </a>
      </div>
    </div>
  </div>

  {{-- ALERT ERROR --}}
  @if ($errors->any())
    <div class="relative rounded-2xl border border-rose-200 bg-rose-50/90 ring-1 ring-rose-100 shadow-sm">
      <div class="flex items-start gap-3 p-4">
        <svg class="h-5 w-5 text-rose-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-width="2" d="M12 9v4m0 4h.01"/><path stroke-width="2" d="M12 2a10 10 0 100 20 10 10 0 000-20z"/>
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
  <form action="{{ route('admin.companies.update', $company) }}" method="POST"
        class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm ring-1 ring-slate-100/80 space-y-6">
    @csrf
    @method('PUT')

    {{-- accent bar kiri --}}
    <div class="absolute inset-y-0 left-0 w-1.5 bg-gradient-to-b from-maroon-700 via-maroon-600 to-maroon-700"></div>

    <div class="px-6 py-6 sm:px-8 sm:py-7 space-y-6">

      {{-- header kecil --}}
      <div class="flex items-center justify-between gap-3 pb-3 border-b border-slate-100">
        <div>
          <h2 class="text-sm font-semibold tracking-wide text-slate-700 uppercase">
            Detail Perusahaan
          </h2>
          <p class="text-xs text-slate-500 mt-0.5">
            Ubah kode, nama, atau deskripsi perusahaan jika diperlukan.
          </p>
        </div>
      </div>

      <div class="grid gap-5 sm:grid-cols-2">
        {{-- Code --}}
        <div class="sm:col-span-1">
          <label class="text-sm font-semibold text-slate-700">Code</label>
          <div class="mt-1 relative w-full">
            <input
              type="text"
              name="code"
              value="{{ old('code', $company->code) }}"
              class="w-full rounded-2xl border border-sky-200 bg-white
                     px-4 py-2.5 text-sm text-slate-700 shadow-sm
                     focus:outline-none focus:ring-2 focus:ring-sky-300/70 focus:border-sky-400"
            >
          </div>
          <p class="text-[11px] text-slate-500 mt-1">
            Unik, tanpa spasi. Boleh pakai dash/underscore.
          </p>
        </div>

        {{-- Active --}}
        <div class="sm:col-span-1 flex items-end">
          <label class="inline-flex items-center gap-2 mt-6">
            <input id="is_active" type="checkbox" name="is_active" value="1"
                   class="rounded border-slate-300 text-maroon-700 focus:ring-maroon-600/30"
                   {{ old('is_active', $company->is_active) ? 'checked' : '' }}>
            <span class="text-sm font-semibold text-slate-700">Active</span>
          </label>
        </div>
      </div>

      {{-- Name --}}
      <div>
        <label class="text-sm font-semibold text-slate-700">Name</label>
        <div class="mt-1 relative w-full">
          <input
            type="text"
            name="name"
            value="{{ old('name', $company->name) }}"
            class="w-full rounded-2xl border border-sky-200 bg-white
                   px-4 py-2.5 text-sm text-slate-700 shadow-sm
                   focus:outline-none focus:ring-2 focus:ring-sky-300/70 focus:border-sky-400"
          >
        </div>
      </div>

      {{-- Description --}}
      <div>
        <label class="text-sm font-semibold text-slate-700">Description (opsional)</label>
        <textarea
          name="description"
          rows="3"
          class="mt-1 w-full rounded-2xl border border-sky-200 bg-white
                 px-4 py-3 text-sm text-slate-700 shadow-sm
                 focus:outline-none focus:ring-2 focus:ring-sky-300/70 focus:border-sky-400"
        >{{ old('description', $company->description) }}</textarea>
      </div>

      {{-- ACTIONS --}}
      <div class="pt-3 flex items-center justify-between gap-3 border-t border-slate-100">
        {{-- Delete (soft) --}}
        <form action="{{ route('admin.companies.destroy', $company) }}" method="POST"
              onsubmit="return confirm('Hapus perusahaan ini? (soft delete)')">
          @csrf
          @method('DELETE')
          <button type="submit"
                  class="px-4 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-sm font-bold">
            Delete
          </button>
        </form>

        <div class="flex items-center gap-2">
          <a href="{{ route('admin.companies.index') }}"
             class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200">
            Cancel
          </a>
          <button type="submit"
                  class="px-5 py-2 rounded-xl bg-maroon-700 hover:bg-maroon-800 text-white text-sm font-bold shadow">
            Save Changes
          </button>
        </div>
      </div>

    </div>
  </form>
</div>
@endsection
