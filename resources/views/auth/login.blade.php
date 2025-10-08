{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login — ARCA</title>
  @vite('resources/css/app.css')
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-white via-slate-50 to-slate-100 text-slate-800">
  <div class="relative flex min-h-screen flex-col lg:flex-row overflow-hidden">

    {{-- Dekorasi blob lembut (maroon only) --}}
    <div class="pointer-events-none absolute -top-24 -left-24 h-72 w-72 rounded-full bg-maroon-300/20 blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-24 -right-24 h-72 w-72 rounded-full bg-maroon-300/20 blur-3xl"></div>

    {{-- LEFT: FORM (card + icons) --}}
    <section class="flex w-full lg:w-1/2 items-center justify-center bg-white/80 backdrop-blur-sm border-b lg:border-b-0 lg:border-r border-maroon-100/40">
      <div class="w-full max-w-md px-6 sm:px-8 py-12 sm:py-14">
        {{-- Brand --}}
        <div class="mb-8 flex items-center gap-3">
          <div class="grid h-10 w-10 place-items-center rounded-xl bg-gradient-to-br from-maroon-700 to-maroon-500 text-white font-bold shadow">AG</div>
          <div>
            <h1 class="text-lg font-bold text-maroon-800 leading-tight">Andalan Group</h1>
            <p class="text-[13px] text-maroon-700/70">BISA — Business Integrated System Application</p>
          </div>
        </div>

        <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-maroon-900">Masuk ke akun Anda</h2>
        <p class="mt-1 text-sm text-slate-600">Gunakan email perusahaan untuk akses ERP.</p>

        {{-- Session status --}}
        @if (session('status'))
          <div class="mt-6 rounded-xl border border-maroon-200 bg-maroon-50 px-4 py-3 text-sm text-maroon-800">
            {{ session('status') }}
          </div>
        @endif

        {{-- CARD FORM --}}
        <div
          x-data="{ show:false }"
          class="mt-6 relative rounded-3xl bg-white/70 ring-1 ring-slate-200/80 shadow-xl overflow-hidden"
        >
          <form method="POST" action="{{ route('login') }}" class="p-6 sm:p-8 space-y-5 relative z-10">
            @csrf

            {{-- Email (with icon) --}}
            <div>
              <label for="email" class="block text-sm font-semibold text-maroon-800">Email</label>
              <div class="mt-2 relative">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center">
                  {{-- Mail icon --}}
                  <svg class="h-5 w-5 text-maroon-600/80" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M2 7.5A2.5 2.5 0 0 1 4.5 5h15A2.5 2.5 0 0 1 22 7.5v9A2.5 2.5 0 0 1 19.5 19h-15A2.5 2.5 0 0 1 2 16.5v-9Zm2.3-.65 7.27 4.54a1.5 1.5 0 0 0 1.56 0L20.4 6.85a1 1 0 0 0-.9-.35H4.5a1 1 0 0 0-.2 0Z"/>
                  </svg>
                </span>
                <input
                  id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                  class="w-full rounded-2xl border border-slate-200 bg-white/80 pl-11 pr-3 py-3 text-sm shadow-sm
                         focus:border-maroon-500 focus:ring-4 focus:ring-maroon-200 outline-none transition"
                  placeholder="nama@andalan.co.id"
                >
              </div>
              @error('email') <p class="mt-2 text-sm text-maroon-700">{{ $message }}</p> @enderror
            </div>

            {{-- Password (with icon + show/hide) --}}
            <div>
              <div class="flex items-center justify-between">
                <label for="password" class="block text-sm font-semibold text-maroon-800">Password</label>
                @if (Route::has('password.request'))
                  <a href="{{ route('password.request') }}" class="text-sm font-medium text-maroon-700 hover:text-maroon-900">Lupa?</a>
                @endif
              </div>
              <div class="mt-2 relative">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center">
                  {{-- Lock icon --}}
                  <svg class="h-5 w-5 text-maroon-600/80" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M12 2a5 5 0 0 1 5 5v3h1a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h1V7a5 5 0 0 1 5-5Zm3 8V7a3 3 0 1 0-6 0v3h6Z"/>
                  </svg>
                </span>
                <input
                  :type="show ? 'text' : 'password'"
                  id="password" name="password" required
                  class="w-full rounded-2xl border border-slate-200 bg-white/80 pl-11 pr-11 py-3 text-sm shadow-sm
                         focus:border-maroon-500 focus:ring-4 focus:ring-maroon-200 outline-none transition"
                  placeholder="••••••••"
                >
                <button
                  type="button"
                  @click="show = !show"
                  class="absolute inset-y-0 right-3 inline-flex items-center justify-center rounded-lg px-2 text-slate-500 hover:text-maroon-700 focus:outline-none"
                  aria-label="Toggle password visibility"
                >
                  <template x-if="!show">
                    {{-- Eye icon --}}
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 5c5.52 0 9.27 3.62 10.74 6.23a1.5 1.5 0 0 1 0 1.54C21.27 15.38 17.52 19 12 19S2.73 15.38 1.26 12.77a1.5 1.5 0 0 1 0-1.54C2.73 8.62 6.48 5 12 5Zm0 3.5A4.5 4.5 0 1 0 12 20a4.5 4.5 0 0 0 0-9Zm0 2a2.5 2.5 0 1 1 0 5 2.5 2.5 0 0 1 0-5Z"/></svg>
                  </template>
                  <template x-if="show">
                    {{-- Eye-off icon --}}
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M3.28 2.22 21.78 20.7l-1.06 1.07-3.07-3.07C15.83 19.34 14 19.99 12 20c-5.52 0-9.27-3.62-10.74-6.23a1.5 1.5 0 0 1 0-1.54C2.3 8.79 5.23 6 9 5.2L2.22 3.28l1.06-1.06ZM9.64 11.5a2.5 2.5 0 0 0 2.86 2.86l-2.86-2.86ZM12 7.5a4.5 4.5 0 0 1 4.5 4.5c0 .6-.12 1.17-.34 1.69l-1.16-1.16A2.5 2.5 0 0 0 12 10.5c-.27 0-.53.04-.77.11l-1.7-1.7A4.45 4.45 0 0 1 12 7.5Z"/></svg>
                  </template>
                </button>
              </div>
              @error('password') <p class="mt-2 text-sm text-maroon-700">{{ $message }}</p> @enderror
            </div>

            {{-- Remember me --}}
            <div class="flex items-center justify-between">
              <label for="remember_me" class="inline-flex items-center gap-2">
                <input id="remember_me" name="remember" type="checkbox"
                       class="rounded border-slate-300 text-maroon-600 focus:ring-maroon-500">
                <span class="text-sm text-slate-700">Ingat saya</span>
              </label>
              <div class="hidden sm:flex items-center gap-1 text-[12px] text-slate-500">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 1 1 0 20 10 10 0 0 1 0-20Zm1 5h-2v6l5 3 .9-1.45L13 12.2V7Z"/></svg>
                <span>Session aman & terenkripsi</span>
              </div>
            </div>

            {{-- Submit --}}
            <button type="submit"
              class="w-full inline-flex justify-center items-center gap-2 rounded-2xl px-4 py-3 font-semibold text-white
                     bg-gradient-to-r from-maroon-700 via-maroon-600 to-maroon-700 shadow-lg shadow-maroon-200/20
                     hover:opacity-95 focus:outline-none focus:ring-4 focus:ring-maroon-200 transition">
              <svg viewBox="0 0 24 24" class="h-5 w-5" fill="currentColor" aria-hidden="true">
                <path d="M13 3a1 1 0 0 1 1 1v6h6a1 1 0 0 1 .8 1.6l-9 11a1 1 0 0 1-1.8-.6v-6H3a1 1 0 0 1-.8-1.6l9-11A1 1 0 0 1 12 3h1Z"/>
              </svg>
              Masuk
            </button>
          </form>
        </div>

        <p class="mt-6 text-[12px] text-slate-500">© {{ date('Y') }} Andalan Group. All rights reserved.</p>
      </div>
    </section>

    {{-- RIGHT: IMAGE PANEL --}}
    <aside class="relative hidden lg:flex lg:w-1/2">
      <img src="{{ asset('assets/images/foto1.png') }}" alt="Andalan Group Operations" class="h-full w-full object-cover" />
      <div class="absolute inset-0 bg-gradient-to-br from-maroon-900/80 via-maroon-700/70 to-maroon-900/80"></div>
      <div class="absolute bottom-12 left-12 right-12 text-white">
        <div class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 ring-1 ring-white/20 backdrop-blur">
          <span class="h-2 w-2 rounded-full bg-white/80"></span>
          <span class="text-xs">ARCA — Secure Access</span>
        </div>
        <h3 class="mt-4 text-3xl font-extrabold drop-shadow">Satu pintu data operasional & keuangan.</h3>
        <p class="mt-2 text-sm text-white/85">Akses dashboard, input harian, dan analitik produksi dalam ekosistem terpadu.</p>
      </div>
    </aside>

  </div>

  {{-- Respect reduced motion --}}
  <style>@media (prefers-reduced-motion: reduce){*{transition:none!important;animation:none!important}}</style>
</body>
</html>
