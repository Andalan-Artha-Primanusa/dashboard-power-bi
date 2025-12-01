{{-- resources/views/layouts/sidenav.blade.php --}}
<aside
    class="bg-maroon-800 text-white flex flex-col h-screen sticky top-0 sidebar-scroll overflow-y-auto transition-all duration-200"
    :class="sidebarExpanded ? 'w-72' : 'w-[4.5rem]'"
>
    {{-- HEADER LOGO --}}
    <div class="flex items-center h-16 px-3 border-b border-maroon-700 bg-maroon-900/95 backdrop-blur">
        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-3 min-w-0"
           :class="sidebarExpanded ? 'justify-start' : 'justify-center w-full'">
            {{-- Logo ARCA --}}
            <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-maroon-700/60 ring-1 ring-maroon-500/70 shadow-md overflow-hidden">
                <img
                    src="{{ asset('assets/images/logoarca.png') }}"
                    alt="Logo ARCA"
                    class="h-8 w-8 object-contain"
                >
            </div>
            <div class="flex flex-col"
                 x-show="sidebarExpanded"
                 x-transition.opacity.duration.150ms>
                <span class="font-extrabold text-xs text-white leading-tight truncate">
                    ARCA Portal
                </span>
                <span class="text-[10px] text-white/70 truncate">
                    Andalan Reporting &amp; Control Analytics
                </span>
            </div>
        </a>
    </div>

    @php
        $u = $u ?? auth()->user();

        // role helper (pakai Spatie / HasRoles)
        $isGM          = $u?->hasRole('gm') ?? false;
        $isSuperAdmin  = $u?->hasRole('super_admin') ?? false;
        $isCreator     = $u?->hasRole('creator') ?? false;
    @endphp

    {{-- USER CARD --}}
    <div class="px-3 pt-3">
        <div class="rounded-2xl bg-gradient-to-br from-maroon-900 to-maroon-800 ring-1 ring-maroon-600 shadow-lg p-3">
            <div class="flex items-center gap-3"
                 :class="sidebarExpanded ? 'justify-start' : 'justify-center'">
                @if($photo)
                    <img src="{{ $photo }}" alt="{{ $name }}"
                         class="h-10 w-10 rounded-full object-cover ring-2 ring-white/60 shadow">
                @else
                    <div class="h-10 w-10 rounded-full bg-white text-maroon-900 flex items-center justify-center font-bold ring-2 ring-white/60 shadow">
                        {{ $initials }}
                    </div>
                @endif

                <div class="min-w-0"
                     x-show="sidebarExpanded"
                     x-transition.opacity.duration.150ms>
                    <div class="text-sm font-extrabold text-white leading-tight truncate">
                        {{ $name }}
                    </div>
                    <div class="mt-0.5 flex items-center gap-2">
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-white text-maroon-900 font-black tracking-wide">
                            {{ $displayRole }}
                        </span>
                    </div>
                    <div class="text-[11px] text-white/80 truncate mt-0.5">
                        {{ $email }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- COMPANY CARD --}}
    <div class="px-3 pt-3" x-data="{ openCompanySwitch:false }">
        <div class="rounded-2xl bg-maroon-900/60 ring-1 ring-maroon-600 p-3">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <div class="text-[10px] uppercase tracking-wide text-white/70"
                         x-show="sidebarExpanded"
                         x-transition.opacity.duration.150ms>
                        Perusahaan Aktif
                    </div>
                    <div class="mt-1 text-xs font-semibold text-white truncate"
                         x-show="sidebarExpanded"
                         x-transition.opacity.duration.150ms>
                        {{ $companyLabel }}
                        @if($activeCompany)
                          <span class="ml-1 text-[10px] px-1.5 py-0.5 rounded bg-white text-maroon-900 font-bold align-middle">
                            {{ $activeCompany->code ?? 'COMP' }}
                          </span>
                        @endif
                    </div>
                </div>

                @if($canSwitchCompany)
                    <button type="button"
                            @click="openCompanySwitch = !openCompanySwitch"
                            class="shrink-0 inline-flex items-center justify-center px-2.5 py-1.5 rounded-xl bg-white text-maroon-900 text-[10px] font-extrabold hover:bg-white/90 shadow">
                        <span x-show="sidebarExpanded">Ganti</span>
                        <span x-show="!sidebarExpanded">🏭</span>
                    </button>
                @endif
            </div>

            @if($canSwitchCompany)
                <div x-cloak x-show="openCompanySwitch && sidebarExpanded" x-transition
                     class="mt-3 rounded-xl border border-maroon-600/70 bg-maroon-900 p-3">
                    <form action="{{ route('company.switch') }}" method="POST" class="space-y-2">
                        @csrf
                        <div class="max-h-44 overflow-y-auto space-y-1 pr-1">
                            @foreach($allowedCompanies as $c)
                                <label class="flex items-center gap-2 rounded-lg px-2 py-1.5 hover:bg-maroon-800 cursor-pointer {{ $activeCompanyId===$c->id ? 'ring-1 ring-white/70 bg-maroon-800' : '' }}">
                                    <input type="radio" name="company_id" value="{{ $c->id }}" class="text-white focus:ring-white"
                                           @checked($activeCompanyId===$c->id)>
                                    <span class="text-sm">
                                      <span class="font-semibold text-white">{{ $c->code ?? 'COMP' }}</span>
                                      <span class="text-white/80">— {{ $c->name }}</span>
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

    {{-- SITE CARD --}}
    <div class="px-3 pt-3" x-data="{ openSwitch:false }">
        <div class="rounded-2xl bg-maroon-900/60 ring-1 ring-maroon-600 p-3">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <div class="text-[10px] uppercase tracking-wide text-white/70"
                         x-show="sidebarExpanded"
                         x-transition.opacity.duration.150ms>
                        Site Aktif
                    </div>
                    <div class="mt-1 text-xs font-semibold text-white truncate"
                         x-show="sidebarExpanded"
                         x-transition.opacity.duration.150ms>
                        {{ $siteLabel }}
                        @if($activeSite)
                          <span class="ml-1 text-[10px] px-1.5 py-0.5 rounded bg-white text-maroon-900 font-bold align-middle">
                            {{ $activeSite->code }}
                          </span>
                        @endif
                    </div>
                </div>

                @if($canSwitchSite)
                    <button type="button"
                            @click="openSwitch = !openSwitch"
                            class="shrink-0 inline-flex items-center justify-center px-2.5 py-1.5 rounded-xl bg-white text-maroon-900 text-[10px] font-extrabold hover:bg-white/90 shadow">
                        <span x-show="sidebarExpanded">Ganti</span>
                        <span x-show="!sidebarExpanded">🏞️</span>
                    </button>
                @endif
            </div>

            @if($canSwitchSite)
                <div x-cloak x-show="openSwitch && sidebarExpanded" x-transition
                     class="mt-3 rounded-xl border border-maroon-600/70 bg-maroon-900 p-3">
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
    <div class="h-px bg-maroon-700/70 mt-3 mx-3"></div>

    {{-- MENU --}}
    <nav class="flex-1 overflow-y-auto px-2 py-3 space-y-1 text-sm">
        {{-- DASHBOARD --}}
        <a href="{{ route('dashboard') }}"
           class="group flex items-center rounded-2xl px-2 py-2 transition-all duration-150"
           :class="sidebarExpanded ? 'justify-start gap-3' : 'justify-center'">
            <div class="flex h-9 w-9 items-center justify-center rounded-2xl border
                {{ request()->routeIs('dashboard')
                    ? 'bg-white text-maroon-900 border-white shadow-lg'
                    : 'bg-maroon-700/40 text-white/90 border-maroon-500/40 group-hover:bg-white group-hover:text-maroon-900 group-hover:border-white/80' }}">
                📊
            </div>
            <span class="truncate text-[13px] font-medium"
                  x-show="sidebarExpanded"
                  x-transition.opacity.duration.150ms>
                Dashboard
            </span>
        </a>

        {{-- POWERBI / DASHBOARDS --}}
        <a href="{{ route('powerbi.sites') }}"
           class="group flex items-center rounded-2xl px-2 py-2 transition-all duration-150"
           :class="sidebarExpanded ? 'justify-start gap-3' : 'justify-center'">
            <div class="flex h-9 w-9 items-center justify-center rounded-2xl border
                {{ request()->routeIs('powerbi.*')
                    ? 'bg-white text-maroon-900 border-white shadow-lg'
                    : 'bg-maroon-700/40 text-white/90 border-maroon-500/40 group-hover:bg-white group-hover:text-maroon-900 group-hover:border-white/80' }}">
                📈
            </div>
            <div class="flex-1 min-w-0"
                 x-show="sidebarExpanded"
                 x-transition.opacity.duration.150ms>
                <div class="truncate text-[13px] font-medium">
                    Dashboards
                </div>
                @if($activeSite)
                    <div class="text-[11px] text-white/75">
                        Site: {{ $activeSite->code }}
                    </div>
                @endif
            </div>
        </a>

        {{-- SUB-LINK BUKA DASHBOARD --}}
        <div class="px-1"
             x-show="sidebarExpanded"
             x-transition.opacity.duration.150ms>
            <a href="{{ $activeSite ? route('powerbi.site.reports', $activeSite) : route('powerbi.sites') }}"
               class="mt-1 w-full inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-[11px] font-medium
                  {{ $activeSite ? 'bg-maroon-700 hover:bg-maroon-600 text-white' : 'bg-maroon-700/60 text-white/70 cursor-pointer' }}">
                ▶ Buka Dashboards ({{ $activeSite?->code ?? 'pilih site' }})
            </a>
        </div>

        {{-- ADMIN MENU --}}
        @if($u && ($isGM || $isSuperAdmin || $isCreator))
            <div class="mt-4 px-3 text-[10px] uppercase tracking-wide text-white/70"
                 x-show="sidebarExpanded"
                 x-transition.opacity.duration.150ms>
                Admin
            </div>

            {{-- Companies --}}
            <a href="{{ route('admin.companies.index') }}"
               class="group flex items-center rounded-2xl px-2 py-2 transition-all duration-150"
               :class="sidebarExpanded ? 'justify-start gap-3' : 'justify-center'">
                <div class="flex h-9 w-9 items-center justify-center rounded-2xl border
                    {{ request()->routeIs('admin.companies.*')
                        ? 'bg-white text-maroon-900 border-white shadow-lg'
                        : 'bg-maroon-700/40 text-white/90 border-maroon-500/40 group-hover:bg-white group-hover:text-maroon-900 group-hover:border-white/80' }}">
                    🏭
                </div>
                <span class="truncate text-[13px] font-medium"
                      x-show="sidebarExpanded"
                      x-transition.opacity.duration.150ms>
                    Companies
                </span>
            </a>

            {{-- Sites --}}
            <a href="{{ route('admin.sites.index') }}"
               class="group flex items-center rounded-2xl px-2 py-2 transition-all duration-150"
               :class="sidebarExpanded ? 'justify-start gap-3' : 'justify-center'">
                <div class="flex h-9 w-9 items-center justify-center rounded-2xl border
                    {{ request()->routeIs('admin.sites.*')
                        ? 'bg-white text-maroon-900 border-white shadow-lg'
                        : 'bg-maroon-700/40 text-white/90 border-maroon-500/40 group-hover:bg-white group-hover:text-maroon-900 group-hover:border-white/80' }}">
                    🏞️
                </div>
                <span class="truncate text-[13px] font-medium"
                      x-show="sidebarExpanded"
                      x-transition.opacity.duration.150ms>
                    Sites
                </span>
            </a>

            {{-- Power BI Admin --}}
            <a href="{{ route('admin.powerbi.index') }}"
               class="group flex items-center rounded-2xl px-2 py-2 transition-all duration-150"
               :class="sidebarExpanded ? 'justify-start gap-3' : 'justify-center'">
                <div class="flex h-9 w-9 items-center justify-center rounded-2xl border
                    {{ request()->routeIs('admin.powerbi.*')
                        ? 'bg-white text-maroon-900 border-white shadow-lg'
                        : 'bg-maroon-700/40 text-white/90 border-maroon-500/40 group-hover:bg-white group-hover:text-maroon-900 group-hover:border-white/80' }}">
                    🧰
                </div>
                <span class="truncate text-[13px] font-medium"
                      x-show="sidebarExpanded"
                      x-transition.opacity.duration.150ms>
                    Power BI Admin
                </span>
            </a>

            {{-- Divisions --}}
            <a href="{{ route('admin.divisions.index') }}"
               class="group flex items-center rounded-2xl px-2 py-2 transition-all duration-150"
               :class="sidebarExpanded ? 'justify-start gap-3' : 'justify-center'">
                <div class="flex h-9 w-9 items-center justify-center rounded-2xl border
                    {{ request()->routeIs('admin.divisions.*')
                        ? 'bg-white text-maroon-900 border-white shadow-lg'
                        : 'bg-maroon-700/40 text-white/90 border-maroon-500/40 group-hover:bg-white group-hover:text-maroon-900 group-hover:border-white/80' }}">
                    🏢
                </div>
                <span class="truncate text-[13px] font-medium"
                      x-show="sidebarExpanded"
                      x-transition.opacity.duration.150ms>
                    Divisions
                </span>
            </a>

            {{-- Users --}}
            <a href="{{ route('admin.users.index') }}"
               class="group flex items-center rounded-2xl px-2 py-2 transition-all duration-150"
               :class="sidebarExpanded ? 'justify-start gap-3' : 'justify-center'">
                <div class="flex h-9 w-9 items-center justify-center rounded-2xl border
                    {{ request()->routeIs('admin.users.*')
                        ? 'bg-white text-maroon-900 border-white shadow-lg'
                        : 'bg-maroon-700/40 text-white/90 border-maroon-500/40 group-hover:bg-white group-hover:text-maroon-900 group-hover:border-white/80' }}">
                    👥
                </div>
                <span class="truncate text-[13px] font-medium"
                      x-show="sidebarExpanded"
                      x-transition.opacity.duration.150ms>
                    Users
                </span>
            </a>
        @endif

        {{-- SECURITY --}}
        @if(
            $u && (
                $isSuperAdmin
                || $isGM
                || $isCreator
                || (method_exists($u,'can') && $u->can('view-audit'))
            )
        )
            <div class="mt-4 px-3 text-[10px] uppercase tracking-wide text-white/70"
                 x-show="sidebarExpanded"
                 x-transition.opacity.duration.150ms>
                Security
            </div>

            {{-- Audit Index --}}
            <a href="{{ route('admin.audit.index') }}"
               class="group flex items-center rounded-2xl px-2 py-2 transition-all duration-150"
               :class="sidebarExpanded ? 'justify-start gap-3' : 'justify-center'">
                <div class="flex h-9 w-9 items-center justify-center rounded-2xl border
                    {{ request()->routeIs('admin.audit.index')
                        ? 'bg-white text-maroon-900 border-white shadow-lg'
                        : 'bg-maroon-700/40 text-white/90 border-maroon-500/40 group-hover:bg-white group-hover:text-maroon-900 group-hover:border-white/80' }}">
                    📜
                </div>
                <span class="truncate text-[13px] font-medium"
                      x-show="sidebarExpanded"
                      x-transition.opacity.duration.150ms>
                    Audit — Index
                </span>
            </a>

            {{-- Audit User --}}
            <a href="{{ route('admin.audit.user', $u?->id) }}"
               class="group flex items-center rounded-2xl px-2 py-2 transition-all duration-150"
               :class="sidebarExpanded ? 'justify-start gap-3' : 'justify-center'">
                <div class="flex h-9 w-9 items-center justify-center rounded-2xl border
                    {{ request()->routeIs('admin.audit.user')
                        ? 'bg-white text-maroon-900 border-white shadow-lg'
                        : 'bg-maroon-700/40 text-white/90 border-maroon-500/40 group-hover:bg-white group-hover:text-maroon-900 group-hover:border-white/80' }}">
                    👤
                </div>
                <span class="truncate text-[13px] font-medium"
                      x-show="sidebarExpanded"
                      x-transition.opacity.duration.150ms>
                    Audit — User
                </span>
            </a>
        @endif
    </nav>

    {{-- LOGOUT --}}
    <div class="border-t border-maroon-700 bg-maroon-900/95 px-3 py-3">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full px-3 py-2 rounded-2xl bg-white text-maroon-900 text-xs font-extrabold hover:bg-white/90 shadow flex items-center justify-center gap-2">
                🚪
                <span x-show="sidebarExpanded" x-transition.opacity.duration.150ms>
                    Keluar dari ARCA
                </span>
            </button>
        </form>
    </div>
</aside>
