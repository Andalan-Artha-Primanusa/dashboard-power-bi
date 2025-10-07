{{-- resources/views/layouts/sidenav.blade.php --}}
<aside class="w-72 bg-maroon-800 text-white flex flex-col h-screen">

  @php
  use Illuminate\Support\Str;
  use Illuminate\Support\Facades\Auth;

  // Hindari error kalau class Site belum diload
  try { $hasSiteModel = class_exists(\App\Models\Site::class); } catch (\Throwable $e) { $hasSiteModel = false; }
  if ($hasSiteModel) { $SiteClass = \App\Models\Site::class; }

  $u = Auth::user();
  try { $u?->loadMissing('role'); } catch (\Throwable $e) {}

  // ========== ROLE ==========
  $rawRole = $u->role->key
      ?? $u->role->slug
      ?? $u->role->name
      ?? (is_string($u->role ?? null) ? $u->role : '')
      ?? '';

  $norm = Str::of($rawRole)->lower()->replace(['_', '-'], ' ')->squish()->toString();
  $map = [
    'general manager' => 'gm',
    'generalmanager'  => 'gm',
    'mgr'             => 'manager',
    'super admin'     => 'super_admin',
    'superadmin'      => 'super_admin',
    'sa'              => 'super_admin',
    'root'            => 'super_admin',
    'gm'              => 'gm',
    'manager'         => 'manager',
  ];
  $roleKey      = $map[$norm] ?? $norm;
  $isSuperAdmin = in_array($roleKey, ['super_admin'], true);
  $isGM         = in_array($roleKey, ['gm'], true);
  $displayRole  = $isSuperAdmin ? 'Super Admin' : (Str::title($roleKey ?: 'User'));

  // ========== USER INFO ==========
  $name     = $u->name  ?? 'User';
  $email    = $u->email ?? '';
  $photo    = property_exists($u,'profile_photo_url') && $u->profile_photo_url ? $u->profile_photo_url : null;
  $initials = collect(preg_split('/\s+/', trim($name)))->filter()->map(fn($p)=>Str::upper(Str::substr($p,0,1)))->take(2)->implode('') ?: 'U';

  // ========== SITE CONTEXT ==========
  $activeSiteId = session('site_id') ?? ($u->default_site_id ?? null);

  // Auto-snap ke default untuk NON-GM
  if (!$isGM && empty(session('site_id')) && !empty($u->default_site_id)) {
      session(['site_id' => $u->default_site_id]);
      $activeSiteId = $u->default_site_id;
  }

  $activeSite   = null;
  $siteLabel    = 'Belum memilih site';

  if ($hasSiteModel && $activeSiteId) {
      try { $activeSite = $SiteClass::find($activeSiteId); } catch (\Throwable $e) {}
  }
  if ($activeSite) {
      $siteLabel = ($activeSite->code ?? 'SITE') . ' — ' . ($activeSite->name ?? '');
  }

  // ====== SWITCH SITE (GM only) ======
  $allowedSites = collect();
  if ($hasSiteModel && $isGM) {
      try {
          $q = method_exists($SiteClass,'active') ? $SiteClass::active() : $SiteClass::query()->where('is_active', true);
          $allowedSites = $q->orderBy('name')->get();
      } catch (\Throwable $e) {}
  }
  $canSwitchSite = $isGM && $allowedSites->isNotEmpty(); // STRICT: hanya GM
  @endphp

  {{-- HEADER --}}
  <div class="flex items-center justify-between h-16 px-4 border-b border-maroon-700 bg-maroon-900/90 backdrop-blur">
    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
      <x-application-logo class="h-8 w-auto text-gold-500" />
      <span class="font-bold text-lg text-gold-400">BISA ERP</span>
    </a>
    @if(($mobile ?? false) === true)
      <button @click="$root.sidebarOpen=false" class="p-2 rounded text-gold-300 hover:text-gold-100 lg:hidden" aria-label="Close sidebar">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    @endif
  </div>

  {{-- USER CARD --}}
  <div class="px-4 pt-4">
    <div class="rounded-2xl bg-gradient-to-br from-maroon-900 to-maroon-800 ring-1 ring-maroon-600 shadow-lg p-4">
      <div class="flex items-center gap-3">
        @if($photo)
          <img src="{{ $photo }}" alt="{{ $name }}" class="h-11 w-11 rounded-full object-cover ring-2 ring-gold-500/80 shadow">
        @else
          <div class="h-11 w-11 rounded-full bg-gold-400 text-maroon-900 flex items-center justify-center font-bold ring-2 ring-gold-500/80 shadow">
            {{ $initials }}
          </div>
        @endif
        <div class="min-w-0">
          <div class="text-base font-extrabold text-gold-100 leading-tight truncate">{{ $name }}</div>
          <div class="mt-0.5 flex items-center gap-2">
            <span class="text-[10px] px-2 py-0.5 rounded-full bg-gold-500 text-maroon-900 font-black tracking-wide">{{ $displayRole }}</span>
          </div>
          <div class="text-[12px] text-gold-200/95 truncate mt-0.5">{{ $email }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- SITE CARD --}}
  <div class="px-4 pt-3" x-data="{ openSwitch:false }">
    <div class="rounded-2xl bg-maroon-900/60 ring-1 ring-maroon-600 p-4">
      <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
          <div class="text-xs uppercase tracking-wide text-gold-300/80">Site Aktif</div>
          <div class="mt-1 text-sm font-semibold text-gold-100 truncate">
            {{ $siteLabel }}
            @if($activeSite)
              <span class="ml-1 text-[10px] px-1.5 py-0.5 rounded bg-gold-500 text-maroon-900 font-bold align-middle">{{ $activeSite->code }}</span>
            @endif
          </div>
          <div class="mt-1 flex items-center gap-1.5 text-[11px]">
            @if($isGM)
              <span class="text-emerald-300">Dapat diganti (GM)</span>
            @else
              <span class="text-amber-300">
                @if($activeSite) Terkunci @else Terkunci — hubungi GM untuk set default @endif
              </span>
            @endif
          </div>
        </div>

        {{-- Tombol "Ganti" -> GM ONLY --}}
        @if($isGM && $allowedSites->isNotEmpty())
          <button type="button" @click="openSwitch = !openSwitch"
                  class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-gold-500 text-maroon-900 text-xs font-extrabold hover:bg-gold-400 shadow">
            Ganti
          </button>
        @endif
      </div>

      {{-- Panel Quick Switch (GM only) --}}
      @if($isGM && $allowedSites->isNotEmpty())
        <div x-cloak x-show="openSwitch" x-transition class="mt-3 rounded-xl border border-maroon-600/70 bg-maroon-900 p-3">
          <form action="{{ route('site.switch') }}" method="POST" class="space-y-2">
            @csrf
            <div class="max-h-44 overflow-y-auto space-y-1 pr-1">
              @foreach($allowedSites as $s)
                <label class="flex items-center gap-2 rounded-lg px-2 py-1.5 hover:bg-maroon-800 cursor-pointer {{ $activeSiteId===$s->id ? 'ring-1 ring-gold-500/60 bg-maroon-800' : '' }}">
                  <input type="radio" name="site_id" value="{{ $s->id }}" class="text-gold-500 focus:ring-gold-500" @checked($activeSiteId===$s->id)>
                  <span class="text-sm">
                    <span class="font-semibold">{{ $s->code }}</span>
                    <span class="text-gold-200/90">— {{ $s->name }}</span>
                    @if(!empty($s->region))
                      <span class="ml-1 text-[11px] text-gold-300/80">({{ $s->region }})</span>
                    @endif
                  </span>
                </label>
              @endforeach
            </div>
            <div class="flex items-center justify-end pt-2">
              <button class="px-3 py-1.5 rounded-lg bg-gold-500 text-maroon-900 text-xs font-extrabold hover:bg-gold-400">Simpan</button>
            </div>
          </form>
        </div>
      @endif
    </div>
  </div>

  {{-- DIVIDER --}}
  <div class="h-px bg-maroon-700/70 mt-3"></div>

  {{-- MENU --}}
  <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-2">
    <a href="{{ route('dashboard') }}"
       class="flex items-center gap-2 px-3 py-2 rounded-lg transition {{ request()->routeIs('dashboard') ? 'bg-gold-500 text-maroon-900 font-semibold shadow' : 'hover:bg-maroon-700 text-white' }}">
      📊 Dashboard
    </a>

    {{-- Power BI --}}
    <a href="{{ route('powerbi.sites') }}"
       class="flex items-center justify-between px-3 py-2 rounded-lg transition {{ request()->routeIs('powerbi.*') ? 'bg-gold-500 text-maroon-900 font-semibold shadow' : 'hover:bg-maroon-700 text-white' }}">
      <span class="inline-flex items-center gap-2">📈 Dashboards</span>
      @if($activeSite)
        <span class="text-[11px] font-semibold {{ request()->routeIs('powerbi.*') ? 'text-maroon-800' : 'text-gold-300' }}">
          {{ $activeSite->code }}
        </span>
      @endif
    </a>

    {{-- Quick open ke dashboard site aktif --}}
    <div class="px-1">
      <a href="{{ $activeSite ? route('powerbi.site.reports', $activeSite) : route('powerbi.sites') }}"
         class="mt-1 w-full inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs
                {{ $activeSite ? 'bg-maroon-700 hover:bg-maroon-600 text-gold-200' : 'bg-maroon-700/60 text-gold-300/70 cursor-pointer' }}">
        ▶ Buka Dashboards ({{ $activeSite?->code ?? 'pilih site' }})
      </a>
    </div>
  </nav>

  {{-- LOGOUT --}}
  <div class="border-t border-maroon-700 bg-maroon-900/90 px-4 py-4">
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button class="w-full px-3 py-2 rounded-xl bg-gold-500 text-maroon-900 text-sm font-extrabold hover:bg-gold-400 shadow">
        <span class="inline-flex items-center gap-2 justify-center">
          Log Out
        </span>
      </button>
    </form>
  </div>

</aside>
