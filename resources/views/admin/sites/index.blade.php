{{-- resources/views/admin/sites/index.blade.php --}}
@extends('layouts.app')

@section('title','Daftar Sites')

@section('content')
<div class="rounded-2xl overflow-hidden shadow ring-1 ring-slate-200 bg-white">

  {{-- Header --}}
  <div class="px-6 py-6 bg-gradient-to-r from-emerald-600 via-[--teal] to-[--navy] text-white flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-bold">🏷️ Manajemen Sites</h1>
      <p class="text-white/85 text-sm mt-1">Kelola site, status aktif, dan pemulihan data terhapus.</p>
    </div>
    <a href="{{ route('admin.sites.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white text-[--navy] font-semibold hover:bg-white/90 shadow-sm">
      + Tambah Site
    </a>
  </div>

  {{-- Toolbar --}}
  <div class="px-6 py-4 flex items-center justify-between">
    <div class="flex items-center gap-2">
      <a href="{{ route('admin.sites.index') }}"
         class="px-3 py-2 rounded-lg text-sm {{ ($only ?? '') !== 'trashed' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700' }}">
        Aktif
      </a>
      <a href="{{ route('admin.sites.index', ['only' => 'trashed']) }}"
         class="px-3 py-2 rounded-lg text-sm {{ ($only ?? '') === 'trashed' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700' }}">
        Terhapus
      </a>
    </div>
    @if (session('success'))
      <div class="text-sm text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-lg border border-emerald-200">
        {{ session('success') }}
      </div>
    @endif
  </div>

  {{-- Table --}}
  <div class="px-6 pb-6 overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead>
        <tr class="text-left text-slate-600 border-b">
          <th class="py-2.5 pr-6">Code</th>
          <th class="py-2.5 pr-6">Name</th>
          <th class="py-2.5 pr-6">Region</th>
          <th class="py-2.5 pr-6">Status</th>
          <th class="py-2.5 pr-6 text-right">Action</th>
        </tr>
      </thead>
      <tbody class="divide-y">
        @forelse($sites as $site)
          <tr>
            <td class="py-3 pr-6 font-mono">{{ $site->code }}</td>
            <td class="py-3 pr-6">{{ $site->name }}</td>
            <td class="py-3 pr-6 text-slate-500">{{ $site->region ?: '—' }}</td>
            <td class="py-3 pr-6">
              @if(method_exists($site, 'trashed') && $site->trashed())
                <span class="inline-flex text-xs px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 ring-1 ring-slate-200">Trashed</span>
              @else
                @if ($site->is_active)
                  <span class="inline-flex text-xs px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 ring-1 ring-emerald-200">Active</span>
                @else
                  <span class="inline-flex text-xs px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 ring-1 ring-amber-200">Inactive</span>
                @endif
              @endif
            </td>
            <td class="py-3 pr-0">
              <div class="flex items-center justify-end gap-2">
                @if(method_exists($site, 'trashed') && $site->trashed())
                  <form action="{{ route('admin.sites.restore', $site->id) }}" method="POST">
                    @csrf
                    <button class="px-2.5 py-1.5 text-xs rounded-lg bg-emerald-600 text-white hover:bg-emerald-700">Restore</button>
                  </form>
                  <form action="{{ route('admin.sites.forceDelete', $site->id) }}" method="POST" onsubmit="return confirm('Hapus permanen?')">
                    @csrf @method('DELETE')
                    <button class="px-2.5 py-1.5 text-xs rounded-lg bg-red-600 text-white hover:bg-red-700">Force Delete</button>
                  </form>
                @else
                  <form action="{{ route('admin.sites.toggle', $site) }}" method="POST">
                    @csrf @method('PATCH')
                    <button class="px-2.5 py-1.5 text-xs rounded-lg bg-slate-800 text-white hover:bg-slate-900">
                      Toggle
                    </button>
                  </form>
                  <a href="{{ route('admin.sites.edit', $site) }}"
                     class="px-2.5 py-1.5 text-xs rounded-lg bg-[--teal] text-white hover:opacity-90">Edit</a>
                  <form action="{{ route('admin.sites.destroy', $site) }}" method="POST" onsubmit="return confirm('Pindah ke trash?')">
                    @csrf @method('DELETE')
                    <button class="px-2.5 py-1.5 text-xs rounded-lg bg-red-600 text-white hover:bg-red-700">Delete</button>
                  </form>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="py-8 text-center text-slate-500">Belum ada data.</td>
          </tr>
        @endforelse
      </tbody>
    </table>

    <div class="mt-4">{{ $sites->links() }}</div>
  </div>
</div>
@endsection
