{{-- resources/views/auth/register.blade.php --}}
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register — ARCA</title>
  @vite('resources/css/app.css')
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <style>
    :root{
      --maroon-900:#3f0f14; --maroon-800:#5a171f; --maroon-700:#7a1e29;
      --maroon-600:#992635; --maroon-500:#b92e41;
    }
  </style>
</head>
<body class="min-h-screen bg-white text-slate-800">
  <div class="relative flex min-h-screen flex-col lg:flex-row overflow-hidden">

    {{-- LEFT IMAGE --}}
    <aside class="relative order-2 lg:order-1 hidden lg:flex lg:w-1/2 bg-[var(--maroon-900)] items-center justify-center">
      <img src="{{ asset('assets/images/foto3.png') }}" alt="Andalan Group" 
           class="max-h-[90vh] max-w-[90%] object-contain object-center" loading="lazy">
      <div class="absolute inset-0 bg-gradient-to-br from-[var(--maroon-900)]/80 via-[var(--maroon-700)]/50 to-[var(--maroon-900)]/80"></div>
      <div class="absolute bottom-12 left-12 right-12 text-white">
        <h3 class="text-3xl font-extrabold drop-shadow">Registrasi akun untuk akses data terpadu lintas divisi.</h3>
        <p class="mt-2 text-sm text-white/85">Mulai gunakan ARCA untuk pelaporan dan kendali di lingkungan Andalan Group.</p>
      </div>
    </aside>

    {{-- RIGHT FORM --}}
    <section class="order-1 lg:order-2 flex w-full lg:w-1/2 items-center justify-center bg-white/90 backdrop-blur border-b lg:border-b-0 lg:border-l border-[var(--maroon-500)]/20">
      <div class="w-full max-w-md px-6 sm:px-8 py-12 sm:py-14">

        {{-- Brand --}}
        <div class="mb-8 flex items-center gap-3">
          <div class="grid h-10 w-10 place-items-center rounded-xl bg-gradient-to-br from-[var(--maroon-800)] to-[var(--maroon-600)] text-white font-bold shadow">AG</div>
          <div>
            <h1 class="text-lg font-bold text-[var(--maroon-800)]">Andalan Group</h1>
            <p class="text-[13px] text-[var(--maroon-700)]/70">ARCA — Andalan Reporting &amp; Control Analytics</p>
          </div>
        </div>

        <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-[var(--maroon-900)]">Daftar Akun Baru</h2>
        <p class="mt-1 text-sm text-slate-600">Gunakan alamat surel perusahaan untuk membuat akun.</p>

        {{-- Form --}}
        <div class="mt-6 relative rounded-3xl bg-white ring-1 ring-slate-200 shadow-xl overflow-hidden">
          <div class="h-1 w-full bg-gradient-to-r from-[var(--maroon-800)] via-[var(--maroon-700)] to-[var(--maroon-800)]"></div>

          <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-5 relative z-10">
            @csrf

            {{-- Foto --}}
            <div x-data="{preview:null}" class="space-y-2">
              <label for="photo" class="block text-sm font-semibold text-[var(--maroon-800)]">Foto / Avatar (opsional)</label>
              <input id="photo" type="file" name="photo" accept="image/*"
                     @change="preview = URL.createObjectURL($event.target.files[0])"
                     class="block w-full text-sm file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0
                            file:bg-[var(--maroon-700)] file:text-white hover:file:bg-[var(--maroon-800)]
                            focus:outline-none">
              @error('photo') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
              <template x-if="preview">
                <img :src="preview" alt="Preview Foto" class="mt-2 h-20 w-20 rounded-xl object-cover ring-1 ring-slate-200">
              </template>
              <p class="text-xs text-slate-500">Format JPG/PNG, maks 2MB. Disarankan 512×512.</p>
            </div>

            {{-- Nama --}}
            <div>
              <label for="name" class="block text-sm font-semibold text-[var(--maroon-800)]">Nama</label>
              <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                     class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-3 py-3 text-sm shadow-sm
                            focus:border-[var(--maroon-600)] focus:ring-4 focus:ring-[var(--maroon-600)]/25 outline-none transition"
                     placeholder="Nama lengkap">
              @error('name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Email --}}
            <div>
              <label for="email" class="block text-sm font-semibold text-[var(--maroon-800)]">Alamat Surel</label>
              <input id="email" type="email" name="email" value="{{ old('email') }}" required
                     class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-3 py-3 text-sm shadow-sm
                            focus:border-[var(--maroon-600)] focus:ring-4 focus:ring-[var(--maroon-600)]/25 outline-none transition"
                     placeholder="nama@andalan.co.id">
              @error('email') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Password --}}
            <div x-data="{ show:false }">
              <label for="password" class="block text-sm font-semibold text-[var(--maroon-800)]">Kata Sandi</label>
              <div class="mt-2 relative">
                <input id="password" :type="show ? 'text' : 'password'" name="password" required autocomplete="new-password"
                       class="w-full rounded-2xl border border-slate-200 bg-white px-3 py-3 pr-10 text-sm shadow-sm
                              focus:border-[var(--maroon-600)] focus:ring-4 focus:ring-[var(--maroon-600)]/25 outline-none transition"
                       placeholder="••••••••">
                <button type="button" @click="show=!show" class="absolute inset-y-0 right-2 px-2 text-slate-500 hover:text-[var(--maroon-800)]">
                  <svg x-show="!show" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 5c5.52 0 9.27 3.62 10.74 6.23a1.5 1.5 0 0 1 0 1.54C21.27 15.38 17.52 19 12 19S2.73 15.38 1.26 12.77a1.5 1.5 0 0 1 0-1.54C2.73 8.62 6.48 5 12 5Zm0 3.5A4.5 4.5 0 1 0 12 20a4.5 4.5 0 0 0 0-9Z"/></svg>
                  <svg x-show="show" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M3.28 2.22 21.78 20.7l-1.06 1.07-3.07-3.07C15.83 19.34 14 19.99 12 20c-5.52 0-9.27-3.62-10.74-6.23a1.5 1.5 0 0 1 0-1.54C2.3 8.79 5.23 6 9 5.2L2.22 3.28l1.06-1.06ZM12 7.5a4.5 4.5 0 0 1 4.5 4.5c0 .6-.12 1.17-.34 1.69l-1.16-1.16A2.5 2.5 0 0 0 12 10.5c-.27 0-.53.04-.77.11l-1.7-1.7A4.45 4.45 0 0 1 12 7.5Z"/></svg>
                </button>
              </div>
              @error('password') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Konfirmasi Password --}}
            <div>
              <label for="password_confirmation" class="block text-sm font-semibold text-[var(--maroon-800)]">Konfirmasi Kata Sandi</label>
              <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                     class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-3 py-3 text-sm shadow-sm
                            focus:border-[var(--maroon-600)] focus:ring-4 focus:ring-[var(--maroon-600)]/25 outline-none transition"
                     placeholder="••••••••">
              @error('password_confirmation') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Submit --}}
            <div class="flex items-center justify-between pt-2">
              <a href="{{ route('login') }}" class="text-sm text-[var(--maroon-700)] hover:text-[var(--maroon-900)]">Sudah punya akun?</a>
              <button type="submit"
                      class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl font-semibold text-white
                             bg-gradient-to-r from-[var(--maroon-800)] via-[var(--maroon-700)] to-[var(--maroon-800)]
                             shadow-lg hover:opacity-95 focus:ring-4 focus:ring-[var(--maroon-600)]/30 transition">
                Daftar
              </button>
            </div>
          </form>
        </div>

        <p class="mt-6 text-[12px] text-slate-500 text-center">© {{ date('Y') }} Andalan Group</p>
      </div>
    </section>

    {{-- Mobile image --}}
    <div class="order-2 lg:hidden relative">
      <div class="relative h-48 w-full overflow-hidden">
        <img src="{{ asset('images/hero-mining.jpg') }}" alt="Andalan Group" class="h-full w-full object-cover object-center">
        <div class="absolute inset-0 bg-gradient-to-t from-[var(--maroon-900)]/60 via-[var(--maroon-700)]/30 to-transparent"></div>
      </div>
    </div>
  </div>
</body>
</html>
