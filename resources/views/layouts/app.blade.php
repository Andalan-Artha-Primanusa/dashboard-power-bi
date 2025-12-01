<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'ARCA — Andalan Reporting & Control Analytics')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Vite (kalau sudah pakai) --}}
    @vite([])

    {{-- Tailwind CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      // CONFIG TAILWIND: definisi warna maroon biar bg-maroon-800 dll hidup
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              maroon: {
                50:  '#fff5f5',
                100: '#ffe3e3',
                200: '#fecaca',
                300: '#fca5a5',
                400: '#f97373',
                500: '#e53935',
                600: '#c62828',
                700: '#992024',
                800: '#6b161c',
                900: '#450910',
              },
            },
          },
        },
      }
    </script>

    {{-- Alpine.js --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .sidebar-scroll::-webkit-scrollbar { width: 6px; }
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: #a8ced2;
            border-radius: 999px;
        }
    </style>

    @stack('head')
</head>

@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Storage;

    $u = Auth::user();
    try { $u?->loadMissing('role'); } catch (\Throwable $e) {}

    // ====== user & role mapping ======
    $rawRole = $u?->role?->key
            ?? $u?->role?->slug
            ?? $u?->role?->name
            ?? (is_string($u->role ?? null) ? $u->role : '')
            ?? '';
    $norm = Str::of($rawRole)->lower()->replace(['_', '-'], ' ')->squish()->toString();
    $roleMap = [
      'general manager' => 'gm',
      'generalmanager'  => 'gm',
      'gm'              => 'gm',
      'mgr'             => 'manager',
      'manager'         => 'manager',
      'super admin'     => 'super_admin',
      'superadmin'      => 'super_admin',
      'sa'              => 'super_admin',
      'root'            => 'super_admin',
      'creator'        => 'creator',
    ];
    $roleKey      = $roleMap[$norm] ?? $norm;
    $isGM         = $roleKey === 'gm';
    $isSuperAdmin = $roleKey === 'super_admin';
    $displayRole  = $isSuperAdmin ? 'Super Admin' : (Str::title($roleKey ?: 'User'));

    // ====== user info ======
    $name  = $u?->name ?? 'User';
    $email = $u?->email ?? '';

    // ====== FOTO / AVATAR FALLBACK ORDER ======
    $photo = null;
    if (!empty($u?->avatar_path)) {
      $photo = Storage::url($u->avatar_path);
    } elseif (!empty($u?->photo_url)) {
      $photo = $u->photo_url;
    } elseif (!empty($u?->profile_photo_url)) {
      $photo = $u->profile_photo_url;
    } elseif (!empty($email)) {
      $hash  = md5(strtolower(trim($email)));
      $photo = "https://www.gravatar.com/avatar/{$hash}?s=160&d=identicon";
    }

    $initials = collect(preg_split('/\s+/', trim($name)))
                  ->filter()
                  ->map(fn($p)=>Str::upper(Str::substr($p,0,1)))
                  ->take(2)->implode('') ?: 'U';

    // ====== model Company (optional) ======
    try { $hasCompanyModel = class_exists(\App\Models\Company::class); }
    catch (\Throwable $e) { $hasCompanyModel = false; }
    if ($hasCompanyModel) { $CompanyClass = \App\Models\Company::class; }

    // ====== company aktif & switching rules ======
    $activeCompanyId = session('company_id') ?? ($u?->default_company_id ?? null);

    if ($u && empty(session('company_id')) && !empty($u->default_company_id)) {
      try { session(['company_id' => $u->default_company_id]); } catch (\Throwable $e) {}
      $activeCompanyId = $u->default_company_id;
    }

    $activeCompany = null;
    if ($hasCompanyModel && $activeCompanyId) {
      try { $activeCompany = $CompanyClass::find($activeCompanyId); } catch (\Throwable $e) {}
    }

    $companyLabel = $activeCompany
      ? (($activeCompany->code ?? 'COMP').' — '.($activeCompany->name ?? ''))
      : 'Belum memilih perusahaan';

    // daftar company yang boleh di-switch
    $allowedCompanies = collect();
    if ($hasCompanyModel) {
      try {
        if ($isGM || $isSuperAdmin) {
          $q = method_exists($CompanyClass,'active')
                ? $CompanyClass::active()
                : $CompanyClass::query()->where('is_active', true);

          $allowedCompanies = $q->orderBy('name')->get();
        } elseif ($u && method_exists($u, 'companies')) {
          $q = $u->companies();
          $q = method_exists($CompanyClass,'active')
                ? $q->active()
                : $q->where('is_active', true);

          $allowedCompanies = $q->orderBy('name')->get();
        }
      } catch (\Throwable $e) {}
    }
    $canSwitchCompany = $allowedCompanies->isNotEmpty();

    // ====== model Site (optional) ======
    try { $hasSiteModel = class_exists(\App\Models\Site::class); }
    catch (\Throwable $e) { $hasSiteModel = false; }
    if ($hasSiteModel) { $SiteClass = \App\Models\Site::class; }

    // ====== site aktif & switching rules ======
    $activeSiteId = session('site_id') ?? ($u?->default_site_id ?? null);

    if ($u && !$isGM && !$isSuperAdmin && empty(session('site_id')) && !empty($u->default_site_id)) {
      try { session(['site_id' => $u->default_site_id]); } catch (\Throwable $e) {}
      $activeSiteId = $u->default_site_id;
    }

    $activeSite = null;
    if ($hasSiteModel && $activeSiteId) {
      try { $activeSite = $SiteClass::find($activeSiteId); } catch (\Throwable $e) {}
    }
    $siteLabel = $activeSite
      ? (($activeSite->code ?? 'SITE').' — '.($activeSite->name ?? ''))
      : 'Belum memilih site';

    $allowedSites = collect();
    if ($hasSiteModel && ($isGM || $isSuperAdmin)) {
      try {
        $q = method_exists($SiteClass,'active')
          ? $SiteClass::active()
          : $SiteClass::query()->where('is_active',true);

        $allowedSites = $q->orderBy('name')->get();
      } catch (\Throwable $e) {}
    }
    $canSwitchSite = ($isGM || $isSuperAdmin) && $allowedSites->isNotEmpty();
@endphp

<body class="bg-[#edf3fb]"
      x-data="{ sidebarExpanded: true }">

<div class="min-h-screen flex">

    {{-- ===== SIDENAV (PARTIAL) ===== --}}
    @include('layouts.sidenav', [
        'u'                => $u,
        'name'             => $name,
        'email'            => $email,
        'photo'            => $photo,
        'initials'         => $initials,
        'displayRole'      => $displayRole,
        'companyLabel'     => $companyLabel,
        'activeCompany'    => $activeCompany,
        'activeCompanyId'  => $activeCompanyId,
        'canSwitchCompany' => $canSwitchCompany,
        'allowedCompanies' => $allowedCompanies,
        'siteLabel'        => $siteLabel,
        'activeSite'       => $activeSite,
        'activeSiteId'     => $activeSiteId,
        'canSwitchSite'    => $canSwitchSite,
        'allowedSites'     => $allowedSites,
        'isGM'             => $isGM,
        'isSuperAdmin'     => $isSuperAdmin,
    ])

    {{-- ===== MAIN AREA (NAVBAR + CONTENT) ===== --}}
    <div class="flex-1 flex flex-col min-w-0">

        {{-- NAVBAR ATAS --}}
        <header class="h-14 bg-white border-b border-slate-200 flex items-center justify-between px-3 sm:px-5">
            <div class="flex items-center gap-3">
                {{-- tombol besar/kecil sidebar --}}
                <button type="button"
                        @click="sidebarExpanded = !sidebarExpanded"
                        class="inline-flex items-center justify-center h-8 w-8 rounded-full border border-slate-200 bg-white text-slate-700 hover:bg-slate-50">
                    <span x-show="sidebarExpanded">«</span>
                    <span x-show="!sidebarExpanded">»</span>
                </button>

                <div class="flex flex-col">
                    <span class="text-sm font-semibold text-slate-900 leading-tight">
                        @yield('title', 'Dashboard')
                    </span>
                    <span class="text-xs text-slate-500 hidden sm:inline">
                        {{ $companyLabel }} @if($activeSite) · {{ $activeSite->code }} — {{ $activeSite->name }} @endif
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                @if($activeSite)
                    <span class="hidden sm:inline-flex items-center rounded-full border border-slate-200 px-2.5 py-1 text-[11px] text-slate-600 bg-slate-50">
                        📍 {{ $activeSite->code }} — {{ $activeSite->name }}
                    </span>
                @endif

                <div class="inline-flex items-center gap-2">
                    @if($photo)
                        <img src="{{ $photo }}" class="h-8 w-8 rounded-full object-cover ring-1 ring-slate-200" alt="{{ $name }}">
                    @else
                        <div class="h-8 w-8 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center text-xs font-semibold">
                            {{ $initials }}
                        </div>
                    @endif
                    <span class="hidden sm:inline text-xs text-slate-700 font-medium">{{ $name }}</span>
                </div>
            </div>
        </header>

        {{-- CONTENT --}}
        <main class="flex-1 overflow-y-auto bg-[#edf3fb]">
            {{-- FULL WIDTH, nggak pakai max-w lagi --}}
            <div class="w-full px-4 sm:px-6 lg:px-8 xl:px-10 py-6">
                @yield('content')
            </div>
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>
