{{-- resources/views/errors/404.blade.php --}}
@extends('layouts.app')

@section('title', '404 Not Found')

@section('content')
<div class="min-h-[60vh] flex flex-col items-center justify-center text-center space-y-6">
  <div class="text-9xl font-extrabold text-slate-300">404</div>
  <h1 class="text-3xl font-bold text-slate-700">Halaman Tidak Ditemukan</h1>
  <p class="text-slate-500">Maaf, halaman yang Anda cari tidak tersedia atau sudah dipindahkan.</p>

  <a href="{{ url('/') }}"
     class="inline-flex items-center px-6 py-3 rounded-xl bg-maroon-700 text-white font-semibold hover:bg-maroon-800">
    ← Kembali ke Beranda
  </a>
</div>
@endsection
