{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login — ARCA</title>
  @vite('resources/css/app.css')
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <style>
    :root{
      --maroon-900:#3f0f14; --maroon-800:#5a171f; --maroon-700:#7a1e29;
      --maroon-600:#992635; --maroon-500:#b92e41; --ink:#0f172a;
    }
    .noise{
      background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.8' numOctaves='2' stitchTiles='stitch'/%3E%3CfeColorMatrix type='saturate' values='0'/%3E%3CfeComponentTransfer%3E%3CfeFuncA type='table' tableValues='0 0 .02 .05 .08 .1'/%3E%3C/feComponentTransfer%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E");
    }
  </style>
</head>
<body class="min-h-screen bg-white text-slate-800">
  <div class="relative flex min-h-screen flex-col lg:flex-row">

    {{-- BACKDROP: maroon/white, soft & premium --}}
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
      <div class="absolute -top-40 -left-32 h-96 w-96 rounded-full bg-[var(--maroon-500)]/10 blur-3xl"></div>
      <div class="absolute -bottom-40 -right-32 h-[28rem] w-[28rem] rounded-full bg-[var(--maroon-700)]/10 blur-3xl"></div>
      <div class="noise absolute inset-0 opacity-[.05] mix-blend-multiply"></div>
    </div>

    {{-- LEFT: FORM --}}
    <section class="relative flex w-full lg:w-[50%] items-center justify-center border-b lg:border-b-0 lg:border-r border-slate-200/60 bg-white/80 backdrop-blur">
      <div class="w-full max-w-md px-6 sm:px-8 py-12 sm:py-16">
        {{-- Brand header --}}
        <div class="mb-8 flex items-center gap-3">
          <div class="grid h-11 w-11 place-items-center rounded-2xl bg-gradient-to-br from-[var(--maroon-800)] to-[var(--maroon-600)] text-white font-extrabold shadow">AG</div>
          <div>
            <h1 class="text-[17px] font-bold text-[var(--maroon-800)] leading-tight">Andalan Group</h1>
            <p class="text-[12px] text-[var(--maroon-700)]/70">ARCA — Andalan Reporting &amp; Control Analytics</p>
          </div>
        </div>

        {{-- Headline & subheadline (netral lintas divisi) --}}
        <h2 class="text-3xl sm:text-4xl font-black tracking-tight text-[var(--maroon-900)]">Masuk ke Sistem ARCA</h2>
        <p class="mt-1 text-[13px] sm:text-sm text-slate-600">Gunakan alamat surel perusahaan untuk mengakses sistem.</p>

        {{-- Session status --}}
        @if (session('status'))
          <div class="mt-6 rounded-2xl border border-[var(--maroon-500)]/20 bg-[var(--maroon-500)]/10 px-4 py-3 text-sm text-[var(--maroon-800)]">
            {{ session('status') }}
          </div>
        @endif

        {{-- CARD --}}
        <div x-data="{ show:false }" class="mt-6 relative rounded-[1.6rem] bg-white ring-1 ring-slate-200 shadow-xl overflow-hidden">
          <div class="h-1 w-full bg-gradient-to-r from-[var(--maroon-800)] via-[var(--maroon-700)] to-[var(--maroon-800)]"></div>

          <form method="POST" action="{{ route('login') }}" class="p-6 sm:p-8 space-y-5 relative z-10">
            @csrf

            {{-- Alamat Surel --}}
            <div>
              <label for="email" class="block text-[13px] font-semibold text-[var(--maroon-800)]">Alamat Surel</label>
              <div class="mt-2 relative">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center">
                  <svg class="h-[18px] w-[18px] text-[var(--maroon-600)]/85" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M2 7.5A2.5 2.5 0 0 1 4.5 5h15A2.5 2.5 0 0 1 22 7.5v9A2.5 2.5 0 0 1 19.5 19h-15A2.5 2.5 0 0 1 2 16.5v-9Zm2.3-.65 7.27 4.54a1.5 1.5 0 0 0 1.56 0L20.4 6.85a1 1 0 0 0-.9-.35H4.5a1 1 0 0 0-.2 0Z"/>
                  </svg>
                </span>
                <input
                  id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                  class="w-full rounded-2xl border border-slate-200 bg-white pl-10 pr-3 py-3 text-sm shadow-sm
                         focus:border-[var(--maroon-600)] focus:ring-4 focus:ring-[var(--maroon-600)]/20 outline-none transition"
                  placeholder="nama@andalan.co.id">
              </div>
              @error('email') <p class="mt-2 text-sm text-[var(--maroon-700)]">{{ $message }}</p> @enderror
            </div>

            {{-- Kata Sandi --}}
            <div>
              <div class="flex items-center justify-between">
                <label for="password" class="block text-[13px] font-semibold text-[var(--maroon-800)]">Kata Sandi</label>
                @if (Route::has('password.request'))
                  <a href="{{ route('password.request') }}" class="text-[12px] font-medium text-[var(--maroon-700)] hover:text-[var(--maroon-900)]">Lupa Kata Sandi?</a>
                @endif
              </div>
              <div class="mt-2 relative">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center">
                  <svg class="h-[18px] w-[18px] text-[var(--maroon-600)]/85" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M12 2a5 5 0 0 1 5 5v3h1a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h1V7a5 5 0 0 1 5-5Zm3 8V7a3 3 0 1 0-6 0v3h6Z"/>
                  </svg>
                </span>
                <input
                  :type="show ? 'text' : 'password'"
                  id="password" name="password" required
                  class="w-full rounded-2xl border border-slate-200 bg-white pl-10 pr-10 py-3 text-sm shadow-sm
                         focus:border-[var(--maroon-600)] focus:ring-4 focus:ring-[var(--maroon-600)]/20 outline-none transition"
                  placeholder="••••••••">
                <button
                  type="button" @click="show = !show"
                  class="absolute inset-y-0 right-2 inline-flex items-center justify-center rounded-xl px-2 text-slate-500 hover:text-[var(--maroon-800)] focus:outline-none"
                  aria-label="Alihkan visibilitas kata sandi">
                  <template x-if="!show">
                    <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="currentColor"><path d="M12 5c5.52 0 9.27 3.62 10.74 6.23a1.5 1.5 0 0 1 0 1.54C21.27 15.38 17.52 19 12 19S2.73 15.38 1.26 12.77a1.5 1.5 0 0 1 0-1.54C2.73 8.79 5.23 6 9 5.2L2.22 3.28l1.06-1.06ZM12 7.5a4.5 4.5 0 0 1 4.5 4.5c0 .6-.12 1.17-.34 1.69l-1.16-1.16A2.5 2.5 0 0 0 12 10.5c-.27 0-.53.04-.77.11l-1.7-1.7A4.45 4.45 0 0 1 12 7.5Z"/></svg>
                  </template>
                  <template x-if="show">
                    <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="currentColor"><path d="M3.28 2.22 21.78 20.7l-1.06 1.07-3.07-3.07C15.83 19.34 14 19.99 12 20c-5.52 0-9.27-3.62-10.74-6.23a1.5 1.5 0 0 1 0-1.54C2.3 8.79 5.23 6 9 5.2L2.22 3.28l1.06-1.06ZM12 7.5a4.5 4.5 0 0 1 4.5 4.5c0 .6-.12 1.17-.34 1.69l-1.16-1.16A2.5 2.5 0 0 0 12 10.5c-.27 0-.53.04-.77.11l-1.7-1.7A4.45 4.45 0 0 1 12 7.5Z"/></svg>
                  </template>
                </button>
              </div>
              @error('password') <p class="mt-2 text-sm text-[var(--maroon-700)]">{{ $message }}</p> @enderror
            </div>

            {{-- Remember + badge --}}
            <div class="flex items-center justify-between">
              <label for="remember_me" class="inline-flex items-center gap-2">
                <input id="remember_me" name="remember" type="checkbox"
                       class="rounded border-slate-300 text-[var(--maroon-700)] focus:ring-[var(--maroon-600)]">
                <span class="text-[13px] text-slate-700">Ingat saya pada perangkat ini</span>
              </label>
              <div class="hidden sm:flex items-center gap-1 text-[11px] text-slate-500">
                <svg class="h-[14px] w-[14px]" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 1 1 0 20 10 10 0 0 1 0-20Zm1 5h-2v6l5 3 .9-1.45L13 12.2V7Z"/></svg>
                <span>Sesi terenkripsi</span>
              </div>
            </div>

            {{-- Submit --}}
            <button type="submit"
              class="w-full inline-flex justify-center items-center gap-2 rounded-2xl px-4 py-3 font-bold text-white
                     bg-gradient-to-r from-[var(--maroon-800)] via-[var(--maroon-700)] to-[var(--maroon-800)]
                     shadow-[0_10px_30px_-10px_rgba(122,30,41,.55)]
                     hover:opacity-95 focus:outline-none focus:ring-4 focus:ring-[var(--maroon-600)]/30 transition">
              <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="currentColor" aria-hidden="true">
                <path d="M13 3a1 1 0 0 1 1 1v6h6a1 1 0 0 1 .8 1.6l-9 11a1 1 0 0 1-1.8-.6v-6H3a1 1 0 0 1-.8-1.6l9-11A1 1 0 0 1 12 3h1Z"/>
              </svg>
              Masuk ke ARCA
            </button>
          </form>
        </div>

        <p class="mt-6 text-[11px] text-slate-500">© {{ date('Y') }} Andalan Group. Seluruh hak cipta dilindungi.</p>
      </div>
    </section>

    {{-- RIGHT: IMAGE PANEL — gunakan foto yang disediakan --}}
    <aside class="relative w-full lg:w-[50%]">
      {{-- Mobile image on top --}}
      <div class="lg:hidden">
        <div class="relative h-48 w-full overflow-hidden rounded-b-[2rem]">
          <img src="{{ asset('assets/images/foto1.png') }}" alt="Andalan Group" class="h-full w-full object-cover">
          <div class="absolute inset-0 bg-gradient-to-t from-[var(--maroon-900)]/40 via-[var(--maroon-700)]/20 to-transparent"></div>
        </div>
      </div>

      {{-- Desktop image with luxe overlay --}}
      <div class="relative hidden h-full w-full lg:block">
        <img src="{{ asset('assets/images/foto1.png') }}" alt="Andalan Group" class="h-full w-full object-cover" />
        <div class="absolute inset-0 bg-gradient-to-br from-[var(--maroon-900)]/70 via-[var(--maroon-700)]/55 to-[var(--maroon-900)]/75"></div>

        {{-- Floating chip --}}
        <div class="absolute top-10 left-10">
          <div class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 ring-1 ring-white/20 backdrop-blur">
            <span class="h-2 w-2 rounded-full bg-white/80"></span>
            <span class="text-xs text-white/90">ARCA — Akses Aman</span>
          </div>
        </div>

        {{-- Headline & tags (netral lintas divisi) --}}
        <div class="absolute bottom-12 left-12 right-12 text-white">
          <h3 class="text-3xl xl:text-4xl font-black drop-shadow-sm leading-tight">Satu Gerbang Pelaporan &amp; Kendali Perusahaan</h3>
          <p class="mt-2 text-sm text-white/85 max-w-xl">
            Layanan terpusat untuk pelaporan dan kendali lintas divisi dalam ekosistem terpadu.
          </p>
          <div class="mt-4 flex flex-wrap gap-2">
            <div class="text-[11px] px-3 py-1 rounded-full bg-white/10 ring-1 ring-white/20 backdrop-blur">Multi-Situs</div>
            <div class="text-[11px] px-3 py-1 rounded-full bg-white/10 ring-1 ring-white/20 backdrop-blur">Berbasis Peran</div>
            <div class="text-[11px] px-3 py-1 rounded-full bg-white/10 ring-1 ring-white/20 backdrop-blur">Jejak Audit</div>
          </div>
        </div>
      </div>
    </aside>

  </div>

  {{-- A11y: reduce motion --}}
  <style>@media (prefers-reduced-motion: reduce){*{transition:none!important;animation:none!important}}</style>
</body>
</html>
