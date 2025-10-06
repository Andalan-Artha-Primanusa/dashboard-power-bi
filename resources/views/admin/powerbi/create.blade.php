@extends('layouts.app')
@section('title','Tambah Power BI')

@section('content')
<form method="POST" action="{{ route('admin.powerbi.store') }}" class="space-y-6 max-w-3xl">@csrf
  <div class="p-4 rounded-xl ring-1 ring-slate-200 space-y-3">
    <label class="block">Nama <input name="name" class="w-full border rounded" required></label>
    <label class="block">Embed URL <input name="embed_url" type="url" class="w-full border rounded" required></label>
    <div class="grid grid-cols-2 gap-3">
      <label><input type="checkbox" name="show_filter_pane" value="1"> Filter Pane</label>
      <label><input type="checkbox" name="show_nav_pane" value="1" checked> Nav Pane</label>
      <label><input type="checkbox" name="show_toolbar" value="1" checked> Toolbar</label>
      <label><input type="checkbox" name="allow_client_download" value="1" checked> Client Download</label>
    </div>
  </div>

  <div class="p-4 rounded-xl ring-1 ring-slate-200 grid md:grid-cols-2 gap-6">
    <div>
      <h3 class="font-semibold mb-2">Bagikan ke User</h3>
      <div class="max-h-64 overflow-auto space-y-1">
        @foreach($users as $u)
          <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="user_ids[]" value="{{ $u->id }}">
            {{ $u->name }} <span class="text-slate-500">({{ $u->email }})</span>
          </label>
        @endforeach
      </div>
    </div>
    <div>
      <h3 class="font-semibold mb-2">Bagikan ke Divisi</h3>
      <div class="max-h-64 overflow-auto space-y-1">
        @foreach($divisions as $d)
          <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="division_ids[]" value="{{ $d->id }}">
            {{ $d->name }}
          </label>
        @endforeach
      </div>
    </div>
  </div>

  <button class="px-4 py-2 rounded bg-emerald-600 text-white">Simpan</button>
  <a href="{{ route('admin.powerbi.index') }}" class="px-4 py-2 rounded ring-1 ring-slate-300">Batal</a>
</form>
@endsection
