{{-- resources/views/auth/register.blade.php --}}
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register — ARCA</title>
  @vite('resources/css/app.css')
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-white via-amber-50 to-blue-50 text-slate-800">

  <div class="relative flex min-h-screen flex-col lg:flex-row overflow-hidden">
    {{-- Blob dekorasi --}}
    <div class="pointer-events-none absolute -top-24 -left-24 h-72 w-72 rounded-full bg-amber-300/40 blur-3xl"></div>
    <div class="pointer-events-none absolute -bottom-24 -right-24 h-72 w-72 rounded-full bg-blue-300/30 blur-3xl"></div>

    {{-- LEFT: FORM --}}
    <section class="flex w-full lg:w-1/2 items-center justify-center bg-white/80 backdrop-blur-sm border-b lg:border-b-0 lg:border-r border-amber-100">
      <div class="w-full max-w-md px-6 sm:px-8 py-12 sm:py-14">
        
        {{-- Brand --}}
        <div class="mb-8 flex items-center gap-3">
          <div class="grid h-10 w-10 place-items-center rounded-xl bg-gradient-to-br from-maroon-700 to-maroon-500 text-white font-bold shadow">AG</div>
          <div>
            <h1 class="text-lg font-bold text-maroon-800">Andalan Group</h1>
            <p class="text-[13px] text-maroon-700/70">BISA — Business Integrated System Application</p>
          </div>
        </div>

        <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-maroon-900">Daftar Akun Baru</h2>
        <p class="mt-1 text-sm text-slate-600">Gunakan email perusahaan Anda.</p>

        {{-- Form Register --}}
        <div class="mt-6 relative rounded-3xl bg-white/70 ring-1 ring-slate-200/80 shadow-xl overflow-hidden">
          <form method="POST" action="{{ route('register') }}" class="p-6 sm:p-8 space-y-5 relative z-10">
            @csrf

            {{-- Name --}}
            <div>
              <label for="name" class="block text-sm font-semibold text-maroon-800">Nama</label>
              <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                     class="mt-2 w-full rounded-2xl border border-slate-200 bg-white/80 px-3 py-3 text-sm shadow-sm
                            focus:border-maroon-500 focus:ring-4 focus:ring-maroon-200 outline-none transition"
                     placeholder="Nama lengkap">
              @error('name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Email --}}
            <div>
              <label for="email" class="block text-sm font-semibold text-maroon-800">Email</label>
              <input id="email" type="email" name="email" value="{{ old('email') }}" required
                     class="mt-2 w-full rounded-2xl border border-slate-200 bg-white/80 px-3 py-3 text-sm shadow-sm
                            focus:border-maroon-500 focus:ring-4 focus:ring-maroon-200 outline-none transition"
                     placeholder="nama@andalan.co.id">
              @error('email') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Password --}}
            <div>
              <label for="password" class="block text-sm font-semibold text-maroon-800">Password</label>
              <input id="password" type="password" name="password" required autocomplete="new-password"
                     class="mt-2 w-full rounded-2xl border border-slate-200 bg-white/80 px-3 py-3 text-sm shadow-sm
                            focus:border-maroon-500 focus:ring-4 focus:ring-maroon-200 outline-none transition"
                     placeholder="••••••••">
              @error('password') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Confirm Password --}}
            <div>
              <label for="password_confirmation" class="block text-sm font-semibold text-maroon-800">Konfirmasi Password</label>
              <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                     class="mt-2 w-full rounded-2xl border border-slate-200 bg-white/80 px-3 py-3 text-sm shadow-sm
                            focus:border-maroon-500 focus:ring-4 focus:ring-maroon-200 outline-none transition"
                     placeholder="••••••••">
              @error('password_confirmation') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Submit --}}
            <div class="flex items-center justify-between pt-2">
              <a href="{{ route('login') }}" class="text-sm text-maroon-700 hover:text-maroon-900">Sudah punya akun?</a>
              <button type="submit"
                      class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl font-semibold text-white
                             bg-gradient-to-r from-maroon-700 via-maroon-600 to-amber-500 shadow-lg hover:opacity-95
                             focus:ring-4 focus:ring-blue-300 transition">
                Daftar
              </button>
            </div>
          </form>
        </div>

        <p class="mt-6 text-[12px] text-slate-500 text-center">© {{ date('Y') }} Andalan Group</p>
      </div>
    </section>

    {{-- RIGHT: IMAGE PANEL --}}
    <aside class="relative hidden lg:flex lg:w-1/2">
      <img src="{{ asset('images/hero-mining.jpg') }}" alt="Andalan Group Operations" class="h-full w-full object-cover">
      <div class="absolute inset-0 bg-gradient-to-br from-maroon-900/80 via-maroon-700/60 to-blue-900/70"></div>
      <div class="absolute bottom-12 left-12 right-12 text-white">
        <div class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 ring-1 ring-white/20 backdrop-blur">
          <span class="h-2 w-2 rounded-full bg-amber-400"></span>
          <span class="text-xs">ARCA — Secure Access</span>
        </div>
        <h3 class="mt-4 text-3xl font-extrabold drop-shadow">Registrasi akun untuk akses data terpadu.</h3>
        <p class="mt-2 text-sm text-white/80">Mulai kelola operasional, keuangan, dan dashboard ARCA.</p>
      </div>
    </aside>
  </div>

</body>
</html>
