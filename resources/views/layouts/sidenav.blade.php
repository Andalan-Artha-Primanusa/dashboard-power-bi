{{-- resources/views/layouts/sidenav.blade.php --}}
<aside class="w-72 bg-maroon-800 text-white flex flex-col h-full">
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

    // daftar site untuk switch (GM & SA bisa)
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

  {{-- HEADER (tanpa tombol apa-apa) --}}
  <div class="flex items-center h-16 px-4 border-b border-maroon-700 bg-maroon-900/90 backdrop-blur">
    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 min-w-0">
      <x-application-logo class="h-8 w-auto text-white shrink-0" />
      <span class="font-bold text-sm text-white/90 truncate">
        ARCA — Andalan Reporting & Control Analytics
      </span>
    </a>
  </div>

  {{-- USER CARD --}}
  <div class="px-4 pt-4">
    <div class="rounded-2xl bg-gradient-to-br from-maroon-900 to-maroon-800 ring-1 ring-maroon-600 shadow-lg p-4">
      <div class="flex items-center gap-3">
        @if($photo)
          <img src="{{ $photo }}" alt="{{ $name }}" class="h-11 w-11 rounded-full object-cover ring-2 ring-white/60 shadow">
        @else
          <div class="h-11 w-11 rounded-full bg-white text-maroon-900 flex items-center justify-center font-bold ring-2 ring-white/60 shadow">
            {{ $initials }}
          </div>
        @endif

        <div class="min-w-0">
          <div class="text-base font-extrabold text-white leading-tight truncate">{{ $name }}</div>
          <div class="mt-0.5 flex items-center gap-2">
            <span class="text-[10px] px-2 py-0.5 rounded-full bg-white text-maroon-900 font-black tracking-wide">
              {{ $displayRole }}
            </span>
          </div>
          <div class="text-[12px] text-white/80 truncate mt-0.5">{{ $email }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- SITE CARD + QUICK SWITCH --}}
  <div class="px-4 pt-3" x-data="{ openSwitch:false }">
    <div class="rounded-2xl bg-maroon-900/60 ring-1 ring-maroon-600 p-4">
      <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
          <div class="text-xs uppercase tracking-wide text-white/70">Site Aktif</div>
          <div class="mt-1 text-sm font-semibold text-white truncate">
            {{ $siteLabel }}
            @if($activeSite)
              <span class="ml-1 text-[10px] px-1.5 py-0.5 rounded bg-white text-maroon-900 font-bold align-middle">
                {{ $activeSite->code }}
              </span>
            @endif
          </div>
          <div class="mt-1 flex items-center gap-1.5 text-[11px]">
            @if($isGM || $isSuperAdmin)
              <span class="text-emerald-300">Dapat diganti ({{ $isSuperAdmin ? 'Super Admin' : 'GM' }})</span>
            @else
              <span class="text-white/80">
                @if($activeSite) Terkunci @else Terkunci — hubungi GM untuk set default @endif
              </span>
            @endif
          </div>
        </div>

        @if($canSwitchSite)
          <button type="button" @click="openSwitch = !openSwitch"
                  class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white text-maroon-900 text-xs font-extrabold hover:bg-white/90 shadow">
            Ganti
          </button>
        @endif
      </div>

      @if($canSwitchSite)
        <div x-cloak x-show="openSwitch" x-transition class="mt-3 rounded-xl border border-maroon-600/70 bg-maroon-900 p-3">
          <form action="{{ route('site.switch') }}" method="POST" class="space-y-2">
            @csrf
            <div class="max-h-44 overflow-y-auto space-y-1 pr-1">
              @foreach($allowedSites as $s)
                <label class="flex items-center gap-2 rounded-lg px-2 py-1.5 hover:bg-maroon-800 cursor-pointer {{ $activeSiteId===$s->id ? 'ring-1 ring-white/70 bg-maroon-800' : '' }}">
                  <input type="radio" name="site_id" value="{{ $s->id }}" class="text-white focus:ring-white"
                         @checked($activeSiteId===$s->id)>
                  <span class="text-sm">
                    <span class="font-semibold text-white">{{ $s->code }}</span>
                    <span class="text-white/80">— {{ $s->name }}</span>
                    @if(!empty($s->region))
                      <span class="ml-1 text-[11px] text-white/70">({{ $s->region }})</span>
                    @endif
                  </span>
                </label>
              @endforeach
            </div>
            <div class="flex items-center justify-end pt-2">
              <button type="submit" class="px-3 py-1.5 rounded-lg bg-white text-maroon-900 text-xs font-extrabold hover:bg-white/90">
                Simpan
              </button>
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
       class="flex items-center gap-2 px-3 py-2 rounded-lg transition
              {{ request()->routeIs('dashboard') ? 'bg-white text-maroon-900 font-semibold shadow' : 'hover:bg-maroon-700 text-white' }}">
      📊 Dashboard
    </a>

    <a href="{{ route('powerbi.sites') }}"
       class="flex items-center justify-between px-3 py-2 rounded-lg transition
              {{ request()->routeIs('powerbi.*') ? 'bg-white text-maroon-900 font-semibold shadow' : 'hover:bg-maroon-700 text-white' }}">
      <span class="inline-flex items-center gap-2">📈 Dashboards</span>
      @if($activeSite)
        <span class="text-[11px] font-semibold {{ request()->routeIs('powerbi.*') ? 'text-maroon-800' : 'text-white/80' }}">
          {{ $activeSite->code }}
        </span>
      @endif
    </a>

    <div class="px-1">
      <a href="{{ $activeSite ? route('powerbi.site.reports', $activeSite) : route('powerbi.sites') }}"
         class="mt-1 w-full inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs
                {{ $activeSite ? 'bg-maroon-700 hover:bg-maroon-600 text-white' : 'bg-maroon-700/60 text-white/70 cursor-pointer' }}">
        ▶ Buka Dashboards ({{ $activeSite?->code ?? 'pilih site' }})
      </a>
    </div>

    @if($u && ($isGM || $isSuperAdmin))
      <div class="mt-3 px-3 text-[11px] uppercase tracking-wide text-white/70">Admin</div>

      <a href="{{ route('admin.sites.index') }}"
         class="flex items-center gap-2 px-3 py-2 rounded-lg transition
                {{ request()->routeIs('admin.sites.*') ? 'bg-white text-maroon-900 font-semibold shadow' : 'hover:bg-maroon-700 text-white' }}">
        🏞️ Sites
      </a>

      <a href="{{ route('admin.powerbi.index') }}"
         class="flex items-center gap-2 px-3 py-2 rounded-lg transition
                {{ request()->routeIs('admin.powerbi.*') ? 'bg-white text-maroon-900 font-semibold shadow' : 'hover:bg-maroon-700 text-white' }}">
        🧰 Power BI Admin
      </a>

      <a href="{{ route('admin.divisions.index') }}"
         class="flex items-center gap-2 px-3 py-2 rounded-lg transition
                {{ request()->routeIs('admin.divisions.*') ? 'bg-white text-maroon-900 font-semibold shadow' : 'hover:bg-maroon-700 text-white' }}">
        🏢 Divisions
      </a>

      <a href="{{ route('admin.users.index') }}"
         class="flex items-center gap-2 px-3 py-2 rounded-lg transition
                {{ request()->routeIs('admin.users.*') ? 'bg-white text-maroon-900 font-semibold shadow' : 'hover:bg-maroon-700 text-white' }}">
        👥 Users
      </a>
    @endif

    @if(($u && $isSuperAdmin) || ($u && method_exists($u,'can') && $u->can('view-audit')))
      <div class="mt-3 px-3 text-[11px] uppercase tracking-wide text-white/70">Security</div>

      <a href="{{ route('admin.audit.index') }}"
         class="flex items-center gap-2 px-3 py-2 rounded-lg transition
                {{ request()->routeIs('admin.audit.index') ? 'bg-white text-maroon-900 font-semibold shadow' : 'hover:bg-maroon-700 text-white' }}">
        📜 Audit — Index
      </a>

      <a href="{{ route('admin.audit.user', $u?->id) }}"
         class="flex items-center gap-2 px-3 py-2 rounded-lg transition
                {{ request()->routeIs('admin.audit.user') ? 'bg-white text-maroon-900 font-semibold shadow' : 'hover:bg-maroon-700 text-white' }}">
        👤 Audit — User
      </a>
    @endif
  </nav>

  {{-- LOGOUT --}}
  <div class="border-t border-maroon-700 bg-maroon-900/90 px-4 py-4">
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit"
              class="w-full px-3 py-2 rounded-xl bg-white text-maroon-900 text-sm font-extrabold hover:bg-white/90 shadow">
        <span class="inline-flex items-center gap-2 justify-center">
          🚪 Log Out
        </span>
      </button>
    </form>
  </div>
</aside>
