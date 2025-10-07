{{-- resources/views/admin/users/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit User')

@section('header')
    ✏️ Edit User: {{ $user->name }}
@endsection

@section('content')
<div class="bg-white shadow rounded-2xl ring-1 ring-slate-200 overflow-hidden">

  {{-- PASSWORD BANNER (tampil sekali) --}}
  @if (session()->has('generated_password'))
    <div class="px-6 pt-6">
      <div x-data="{open:true, copied:false}" x-show="open" x-transition
           class="relative mb-4 overflow-hidden rounded-2xl bg-white ring-1 ring-[#f6c74d] shadow-[inset_0_0_0_1px_#f6c74d]">
        <div class="bg-gradient-to-r from-maroon-700 via-maroon-600 to-yellow-600 px-4 py-2 text-white text-xs font-semibold tracking-wide">
          🔐 Password Baru — tampil sekali, segera salin
        </div>
        <div class="p-4 flex items-start gap-3">
          <svg class="h-5 w-5 text-yellow-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="2" d="M12 9v4m0 4h.01" />
            <path stroke-width="2" d="M12 2a10 10 0 100 20 10 10 0 000-20z" />
          </svg>
          <div class="text-sm text-slate-800">
            <div class="flex items-center gap-2 flex-wrap">
              <span class="text-slate-600">Password:</span>
              <code id="__pwd" class="px-2 py-1 rounded-lg bg-slate-100 ring-1 ring-slate-200 font-mono text-[13px]">
                {{ session('generated_password') }}
              </code>
              <button
                @click="navigator.clipboard.writeText(document.getElementById('__pwd').innerText.trim()); copied=true"
                class="inline-flex items-center gap-1 rounded-lg px-2 py-1 text-xs ring-1 ring-slate-300 hover:bg-slate-50">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <rect x="9" y="9" width="13" height="13" rx="2" />
                  <rect x="2" y="2" width="13" height="13" rx="2" />
                </svg>
                <span x-show="!copied">Copy</span>
                <span x-show="copied" class="text-emerald-700">Copied!</span>
              </button>
            </div>
            <p class="mt-1 text-xs text-slate-500">Jangan simpan di log. Minta user ganti password setelah login.</p>
          </div>
          <button @click="open=false" class="ml-auto rounded-lg p-1.5 text-slate-600 hover:bg-slate-100">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path d="M18 6 6 18M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
    </div>
  @endif

  <div class="p-6 space-y-8">

    {{-- INFO RINGKAS --}}
    <div class="rounded-2xl ring-1 ring-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
      <div><span class="font-semibold">Nama:</span> {{ $user->name }}</div>
      <div><span class="font-semibold">Email:</span> {{ $user->email }}</div>
      <div class="flex items-center gap-2">
        <span class="font-semibold">Default Site:</span>
        @if($user->defaultSite)
          <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-sky-100 text-sky-700 ring-1 ring-sky-200">
            {{ $user->defaultSite->code }} — {{ $user->defaultSite->name }}
            @if($user->defaultSite->region)
              <span class="ml-1 text-[10px] opacity-80">({{ $user->defaultSite->region }})</span>
            @endif
          </span>
        @else
          <span class="text-slate-500">—</span>
        @endif
      </div>
    </div>

    {{-- UPDATE DIVISION --}}
    <div>
      <form method="POST" action="{{ route('admin.users.updateDivision',$user) }}" class="space-y-2">
        @csrf
        @method('PATCH')

        <label class="block text-sm font-semibold text-slate-700">Division</label>
        <select name="division_id"
                class="mt-1 block w-full rounded-lg border-slate-300 focus:ring-gold-500 focus:border-gold-500">
          <option value="">— None —</option>
          @foreach($divisions as $d)
            <option value="{{ $d->id }}" @selected($user->division_id==$d->id)>{{ $d->name }}</option>
          @endforeach
        </select>
        @error('division_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror

        <button type="submit"
                class="mt-2 px-4 py-2 rounded-xl bg-maroon-700 text-white font-semibold hover:bg-maroon-600">
          Update Division
        </button>
      </form>
    </div>

    {{-- UPDATE DEFAULT SITE --}}
    <div>
      {{-- NOTE: butuh route & method controller updateSite --}}
      <form method="POST" action="{{ route('admin.users.updateSite',$user) }}" class="space-y-2">
        @csrf
        @method('PATCH')

        <label class="block text-sm font-semibold text-slate-700">Default Site</label>
        <select name="default_site_id"
                class="mt-1 block w-full rounded-lg border-slate-300 focus:ring-gold-500 focus:border-gold-500">
          <option value="">— None —</option>
          @foreach($sites as $s)
            <option value="{{ $s->id }}" @selected($user->default_site_id===$s->id)>
              {{ $s->code }} — {{ $s->name }} @if($s->region) ({{ $s->region }}) @endif
            </option>
          @endforeach
        </select>
        @error('default_site_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror

        <button type="submit"
                class="mt-2 px-4 py-2 rounded-xl bg-sky-600 text-white font-semibold hover:bg-sky-500">
          Update Default Site
        </button>
      </form>
    </div>

    {{-- RESET PASSWORD (generate random) --}}
    <div class="pt-2">
      <form method="POST" action="{{ route('admin.users.resetPassword',$user) }}" class="space-y-2">
        @csrf
        <p class="text-sm text-slate-600">
          Menekan tombol ini akan <span class="font-semibold text-slate-800">membuat password acak baru</span>
          dan menampilkannya sekali di atas.
        </p>
        <button type="submit"
                class="px-4 py-2 rounded-xl bg-emerald-600 text-white font-semibold hover:bg-emerald-500">
          🔐 Reset Password (Generate)
        </button>
      </form>
    </div>

  </div>
</div>
@endsection
