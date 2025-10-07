{{-- resources/views/layouts/sidenav.blade.php --}}
<aside class="w-72 bg-maroon-800 text-white flex flex-col h-screen">

  @php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Auth;
    use App\Models\Site;

    $u = Auth::user();
    try { $u?->loadMissing('role'); } catch (\Throwable $e) {}

    // ===== Normalisasi Role =====
    $rawRole = $u->role->key
        ?? $u->role->slug
        ?? $u->role->name
        ?? (is_string($u->role ?? null) ? $u->role : '')
        ?? '';

    $norm = Str::of($rawRole)->lower()->replace(['_', '-'], ' ')->squish()->toString();
    $map  = [
      'gm'              => 'gm',
      'general manager' => 'gm',
      'generalmanager'  => 'gm',
      'mgr'             => 'manager',
      'manager'         => 'manager',
      'super admin'     => 'super_admin',
      'superadmin'      => 'super_admin',
      'sa'              => 'super_admin',
      'root'            => 'super_admin',
    ];
    $roleKey      = $map[$norm] ?? $norm;
    $isSuperAdmin = ($roleKey === 'super_admin');
    $isGM         = ($roleKey === 'gm');
    $displayRole  = $isSuperAdmin ? 'Super Admin' : (Str::title($roleKey ?: 'User'));

    // ===== User Info =====
    $name     = $u->name ?? 'User';
    $email    = $u->email ?? '';
    $photo    = property_exists($u,'profile_photo_url') && $u->profile_photo_url ? $u->profile_photo_url : null;
    $initials = collect(preg_split('/\s+/', trim($name)))->filter()->map(fn($p)=>Str::upper(Str::substr($p,0,1)))->take(2)->implode('') ?: 'U';

    // ===== Site Aktif =====
    $activeSiteId = session('site_id') ?? ($u->default_site_id ?? null);
    $activeSite   = $activeSiteId ? Site::find($activeSiteId) : null;
    $siteLabel    = $activeSite ? (($activeSite->code ?? 'SITE') . ' — ' . ($activeSite->name ?? '')) : 'Belum memilih site';

    $isMobile = ($mobile ?? false) === true;
  @endphp

  {{-- HEADER --}}
  <div class="flex items-center justify-between h-16 px-4 border-b border-maroon-700 bg-maroon-900/90">
    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
      <x-application-logo class="h-8 w-auto text-gold-500" />
      <span class="font-bold text-lg text-gold-400">BISA ERP</span>
    </a>
    @if($isMobile)
      <button @click="$root.sidebarOpen=false" class="p-2 rounded text-gold-300 hover:text-gold-100" aria-label="Close sidebar">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
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
          <div class="mt-0.5">
            <span class="text-[10px] px-2 py-0.5 rounded-full bg-gold-500 text-maroon-900 font-black tracking-wide">
              {{ $displayRole }}
            </span>
          </div>
          <div class="text-[12px] text-gold-200/95 truncate mt-0.5">{{ $email }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- SITE CARD --}}
  <div class="px-4 pt-3">
    <div class="rounded-2xl bg-maroon-900/60 ring-1 ring-maroon-600 p-4">
      <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
          <div class="text-xs uppercase tracking-wide text-gold-300/80">Site Aktif</div>
          <div class="mt-1 text-sm font-semibold text-gold-100 truncate">{{ $siteLabel }}</div>
        </div>
        <a href="{{ route('site.select') }}"
           class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-gold-500 text-maroon-900 text-xs font-extrabold hover:bg-gold-400 shadow">
          Ganti
        </a>
      </div>
    </div>
  </div>

  {{-- DIVIDER --}}
  <div class="h-px bg-maroon-700/70 mt-3"></div>

  {{-- MENU --}}
  <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-2">
    <a href="{{ route('dashboard') }}"
       class="flex items-center gap-2 px-3 py-2 rounded-lg transition
              {{ request()->routeIs('dashboard') ? 'bg-gold-500 text-maroon-900 font-semibold shadow' : 'hover:bg-maroon-700 text-white' }}">
      📊 Dashboard
    </a>

    <a href="{{ route('powerbi.index') }}"
       class="flex items-center gap-2 px-3 py-2 rounded-lg transition
              {{ request()->routeIs('powerbi.*') ? 'bg-gold-500 text-maroon-900 font-semibold shadow' : 'hover:bg-maroon-700 text-white' }}">
      📈 Power BI
    </a>

    {{-- ADMIN SECTION --}}
    @if($u && ($isGM || $isSuperAdmin))
      <div class="mt-3 px-3 text-[11px] uppercase tracking-wide text-gold-300/80">Admin</div>

      {{-- 🏞️ Sites --}}
      <a href="{{ route('admin.sites.index') }}"
         class="flex items-center gap-2 px-3 py-2 rounded-lg transition
                {{ request()->routeIs('admin.sites.*') ? 'bg-gold-500 text-maroon-900 font-semibold shadow' : 'hover:bg-maroon-700 text-white' }}">
        🏞️ Sites
      </a>

      <a href="{{ route('admin.powerbi.index') }}"
         class="flex items-center gap-2 px-3 py-2 rounded-lg transition
                {{ request()->routeIs('admin.powerbi.*') ? 'bg-gold-500 text-maroon-900 font-semibold shadow' : 'hover:bg-maroon-700 text-white' }}">
        🧰 Power BI Admin
      </a>

      <a href="{{ route('admin.divisions.index') }}"
         class="flex items-center gap-2 px-3 py-2 rounded-lg transition
                {{ request()->routeIs('admin.divisions.*') ? 'bg-gold-500 text-maroon-900 font-semibold shadow' : 'hover:bg-maroon-700 text-white' }}">
        🏢 Divisions
      </a>

      <a href="{{ route('admin.users.index') }}"
         class="flex items-center gap-2 px-3 py-2 rounded-lg transition
                {{ request()->routeIs('admin.users.*') ? 'bg-gold-500 text-maroon-900 font-semibold shadow' : 'hover:bg-maroon-700 text-white' }}">
        👥 Users
      </a>
    @endif

    {{-- SECURITY --}}
    @if($u && ($u->can('view-audit') || $isSuperAdmin))
      <div class="mt-3 px-3 text-[11px] uppercase tracking-wide text-gold-300/80">Security</div>
      <a href="{{ route('admin.audit.index') }}"
         class="flex items-center gap-2 px-3 py-2 rounded-lg transition
                {{ request()->routeIs('admin.audit.*') ? 'bg-gold-500 text-maroon-900 font-semibold shadow' : 'hover:bg-maroon-700 text-white' }}">
        📜 Audit Log
      </a>
    @endif
  </nav>

  {{-- LOGOUT --}}
  <div class="border-t border-maroon-700 bg-maroon-900/90 px-4 py-4">
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button class="w-full px-3 py-2 rounded-xl bg-gold-500 text-maroon-900 text-sm font-extrabold hover:bg-gold-400 shadow">
        <span class="inline-flex items-center gap-2 justify-center">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          Log Out
        </span>
      </button>
    </form>
  </div>
</aside>
