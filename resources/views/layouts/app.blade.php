<!doctype html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}" 
      x-data="{ sidebarOpen:false }" 
      :class="sidebarOpen ? 'overflow-hidden' : ''">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>
    @hasSection('title') @yield('title')
    @elseif (!empty($title)) {{ $title }}
    @else {{ config('app.name', 'BERKEMAH') }}
    @endif
  </title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  @vite(['resources/css/app.css','resources/js/app.js'])
  @stack('head')
  <style>[x-cloak]{ display:none !important; }</style>
</head>
<body class="font-sans antialiased bg-gray-100">

  {{-- Mobile topbar --}}
  <div class="lg:hidden sticky top-0 z-40 bg-maroon-800 text-gold-200 border-b border-maroon-700">
    <div class="px-4 h-14 flex items-center justify-between">
      <button @click="sidebarOpen=true" class="p-2 rounded-md hover:bg-maroon-700" aria-label="Open sidebar">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>
      <div class="font-semibold">{{ config('app.name','BERKEMAH') }}</div>
      <div class="w-6"></div>
    </div>
  </div>

  {{-- Wrapper utama full tinggi layar --}}
  <div class="h-screen flex overflow-hidden">

    {{-- Sidebar (desktop) --}}
    <div class="hidden lg:flex lg:h-full">
      {{-- Pastikan di sidenav pakai <aside class="w-72 h-full flex flex-col ..."> --}}
      @include('layouts.sidenav')
    </div>

    {{-- Sidebar (mobile slide-over) --}}
    <div class="lg:hidden">
      <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-50">
        <div class="absolute inset-0 bg-black/30" @click="sidebarOpen=false" aria-hidden="true"></div>
        <div class="absolute inset-y-0 left-0 w-72">
          <div class="h-full shadow-xl bg-maroon-800">
            @include('layouts.sidenav', ['mobile' => true])
          </div>
        </div>
      </div>
    </div>

    {{-- MAIN CONTENT --}}
    <div class="flex-1 min-w-0 min-h-0 flex flex-col">

      {{-- Header via @section("header") --}}
      @hasSection('header')
        <header class="border-b bg-white">
          <div class="py-6 px-4 sm:px-6 lg:px-8 mx-0 max-w-none">
            @yield('header')
          </div>
        </header>
      @endif

      {{-- Header via <x-slot name="header"> --}}
      @if (isset($header))
        <div class="px-4 pt-6 mx-0 max-w-none">
          {{ $header }}
        </div>
      @endif

      {{-- Konten dengan scroll --}}
      <main class="flex-1 w-full px-4 py-8 lg:px-8 mx-0 max-w-none overflow-y-auto">
        {{-- Flash & errors --}}
        @if (session('status'))
          <div class="mb-4 p-3 rounded-lg bg-green-50 text-emerald-700 border border-green-200">
            {{ session('status') }}
          </div>
        @endif
        @if ($errors->any())
          <div class="mb-4 p-3 rounded-lg bg-red-50 text-red-700 border border-red-200">
            <ul class="list-disc list-inside text-sm">
              @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        {{-- Hybrid: slot > section --}}
        @if (isset($slot))
          {{ $slot }}
        @else
          @yield('content')
        @endif
      </main>

      @stack('modals')
      @stack('scripts')
    </div>
  </div>
</body>
</html>
