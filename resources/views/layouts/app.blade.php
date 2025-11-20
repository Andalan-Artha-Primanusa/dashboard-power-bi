{{-- resources/views/layouts/app.blade.php --}}
<!doctype html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}"
      x-data="{
        sidebarOpen: window.innerWidth >= 1024,
        init() {
          window.addEventListener('resize', () => {
            this.sidebarOpen = window.innerWidth >= 1024;
          });
        }
      }"
      x-on:keydown.escape.window="sidebarOpen = false"
      :class="sidebarOpen && window.innerWidth < 1024 ? 'overflow-hidden' : ''">

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
  @php
    /** @var \Illuminate\Support\ViewErrorBag $errors */
    $errors = $errors ?? session('errors', new \Illuminate\Support\ViewErrorBag);
  @endphp

  {{-- 1 TOMBOL TOGGLE (muncul semua device) --}}
  <button
    type="button"
    class="fixed top-4 left-4 z-50 p-2 rounded-lg bg-maroon-800 text-gold-200 shadow hover:bg-maroon-700"
    @click="sidebarOpen = !sidebarOpen"
    aria-label="Toggle sidebar">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
    </svg>
  </button>

  <div class="h-screen flex overflow-hidden relative">

    {{-- DESKTOP SIDEBAR (beneran hilang kalau ditutup) --}}
    <div class="hidden lg:block" x-show="sidebarOpen" x-transition x-cloak>
      @include('layouts.sidenav')
    </div>

    {{-- MOBILE SIDEBAR (slide) --}}
    <div class="lg:hidden fixed inset-y-0 left-0 z-50 w-72 transition-transform duration-200 ease-in-out"
         :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
         x-cloak>
      @include('layouts.sidenav')
    </div>

    {{-- BACKDROP MOBILE --}}
    <div class="fixed inset-0 bg-black/30 z-40 lg:hidden"
         x-show="sidebarOpen"
         x-cloak
         @click="sidebarOpen = false"
         aria-hidden="true"></div>

    {{-- MAIN CONTENT --}}
    <div class="flex-1 min-w-0 min-h-0 flex flex-col">

      @hasSection('header')
        <header class="border-b bg-white">
          <div class="py-6 px-4 sm:px-6 lg:px-8 mx-0 max-w-none">
            @yield('header')
          </div>
        </header>
      @endif

      @if (isset($header))
        <div class="px-4 pt-6 mx-0 max-w-none">
          {{ $header }}
        </div>
      @endif

      <main class="flex-1 w-full px-4 py-6 lg:px-8 mx-0 max-w-none overflow-y-auto">
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

        @isset($slot)
          {{ $slot }}
        @else
          @yield('content')
        @endisset
      </main>

      @stack('modals')
      @stack('scripts')
    </div>
  </div>
</body>
</html>
