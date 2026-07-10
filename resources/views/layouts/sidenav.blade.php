{{-- resources/views/layouts/sidenav.blade.php --}}
@php
 $u = $u ?? auth()->user();
 $isGM = $u?->hasRole('gm') ?? false;
 $isSuperAdmin = $u?->hasRole('super_admin') ?? false;
 $isCreator = $u?->hasRole('creator') ?? false;
 $canSeeAdmin = $u && ($isGM || $isSuperAdmin || $isCreator);
 $canSeeSecurity = $u && (
 $isSuperAdmin
 || $isGM
 || $isCreator
 || (method_exists($u, 'can') && $u->can('view-audit'))
 );

 $icon = function (string $name, string $class = 'h-5 w-5') {
 $attrs = 'class="'.$class.'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"';
 $paths = [
 'dashboard' => '<rect x="3" y="3" width="7" height="8" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="15" width="7" height="6" rx="1.5"/>',
 'chart' => '<path d="M4 19V5"/><path d="M4 19h16"/><path d="M8 16v-5"/><path d="M12 16V8"/><path d="M16 16v-3"/>',
 'building' => '<path d="M4 21V5a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v16"/><path d="M9 21v-4h3v4"/><path d="M8 7h.01"/><path d="M13 7h.01"/><path d="M8 11h.01"/><path d="M13 11h.01"/><path d="M3 21h18"/>',
 'map' => '<path d="m3 6 6-3 6 3 6-3v15l-6 3-6-3-6 3Z"/><path d="M9 3v15"/><path d="M15 6v15"/>',
 'briefcase' => '<path d="M10 6V5a2 2 0 0 1 2-2h0a2 2 0 0 1 2 2v1"/><rect x="3" y="6" width="18" height="14" rx="2"/><path d="M3 12h18"/><path d="M10 12v2h4v-2"/>',
 'layers' => '<path d="m12 3 9 5-9 5-9-5Z"/><path d="m3 13 9 5 9-5"/><path d="m3 18 9 5 9-5"/>',
 'users' => '<path d="M16 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/><circle cx="9.5" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
 'shield' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/>',
 'user' => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
 'switch' => '<path d="M16 3h5v5"/><path d="M21 3 14 10"/><path d="M8 21H3v-5"/><path d="m3 21 7-7"/>',
 'logout' => '<path d="M10 17 15 12l-5-5"/><path d="M15 12H3"/><path d="M21 19V5a2 2 0 0 0-2-2h-6"/><path d="M13 21h6a2 2 0 0 0 2-2"/>',
 'chevron' => '<path d="m9 18 6-6-6-6"/>',
 ];

 return '<svg '.$attrs.'>'.($paths[$name] ?? $paths['dashboard']).'</svg>';
 };

 $navItemClass = 'group flex items-center rounded-xl px-2.5 py-2.5 transition duration-150';
 $iconBoxBase = 'flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border transition duration-150';
@endphp

<style>
 .arca-sidebar-shell {
 border-right: 2px solid #fff;
 box-shadow: inset -1px 0 0 rgba(255, 255, 255, .7), 12px 0 28px rgba(189, 155, 117, .18);
 }

 .arca-sidebar-card {
 box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .34);
 }

 .arca-nav-root {
 border-top: 1px solid rgba(255, 255, 255, .38);
 padding-top: .875rem;
 }

 .arca-nav-root a {
 border-left: 3px solid transparent;
 }

 .arca-nav-root a.bg-white {
 border-left-color: #BD9B75;
 }

 .arca-nav-root a:not(.bg-white):hover {
 border-left-color: #fff;
 }

 .arca-section-heading {
 margin-top: 1rem;
 border-top: 1px solid rgba(255, 255, 255, .34);
 padding-top: 1rem;
 }
</style>

<aside
 class="arca-sidebar-shell sticky top-0 flex h-screen flex-col overflow-hidden bg-maroon-800 text-white shadow-xl transition-all duration-200"
 :class="sidebarExpanded ? 'w-72' : 'w-20'"
>
 <div class="flex h-16 items-center border-b border-white/25 px-3">
 <a href="{{ route('dashboard') }}"
 class="flex min-w-0 items-center gap-3"
 :class="sidebarExpanded ? 'w-full justify-start' : 'w-full justify-center'">
 <div class="grid h-10 w-10 shrink-0 place-items-center overflow-hidden rounded-xl bg-white ring-1 ring-white">
 <img src="{{ asset('assets/images/logoarca.png') }}" alt="ARCA" class="h-8 w-8 object-contain">
 </div>
 <div class="min-w-0" x-show="sidebarExpanded" x-transition.opacity.duration.150ms>
 <div class="truncate text-sm font-extrabold leading-tight text-white">ARCA Portal</div>
 <div class="truncate text-[11px] font-medium text-white/75">Reporting & Control</div>
 </div>
 </a>
 </div>

 <div class="sidebar-scroll flex-1 overflow-y-auto px-3 py-4">
 <div class="arca-sidebar-card mb-4 rounded-2xl border border-white/25 bg-maroon-900 p-3">
 <div class="flex items-center gap-3" :class="sidebarExpanded ? 'justify-start' : 'justify-center'">
 @if($photo)
 <img src="{{ $photo }}" alt="{{ $name }}" class="h-10 w-10 shrink-0 rounded-xl object-cover ring-2 ring-white">
 @else
 <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-white text-sm font-extrabold text-maroon-900 ring-2 ring-white">
 {{ $initials }}
 </div>
 @endif

 <div class="min-w-0" x-show="sidebarExpanded" x-transition.opacity.duration.150ms>
 <div class="truncate text-sm font-bold text-white">{{ $name }}</div>
 <div class="mt-1 inline-flex rounded-full bg-white px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-maroon-900">
 {{ $displayRole }}
 </div>
 </div>
 </div>
 </div>

 <div class="mb-4 space-y-2" x-show="sidebarExpanded" x-transition.opacity.duration.150ms>
 <div class="arca-sidebar-card rounded-2xl border border-white/25 bg-maroon-900 p-3" x-data="{ openCompanySwitch:false }">
 <div class="flex items-start gap-3">
 <div class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-white text-maroon-900">
 {!! $icon('building', 'h-4 w-4') !!}
 </div>
 <div class="min-w-0 flex-1">
 <div class="text-[10px] font-bold uppercase tracking-wide text-white/70">Perusahaan</div>
 <div class="mt-0.5 truncate text-xs font-semibold text-white">{{ $companyLabel }}</div>
 </div>
 @if($canSwitchCompany)
 <button type="button"
 @click="openCompanySwitch = !openCompanySwitch"
 class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-white text-maroon-900"
 aria-label="Ganti perusahaan">
 {!! $icon('switch', 'h-4 w-4') !!}
 </button>
 @endif
 </div>

 @if($canSwitchCompany)
 <div x-cloak x-show="openCompanySwitch" x-transition class="mt-3 border-t border-white/25 pt-3">
 <form action="{{ route('company.switch') }}" method="POST" class="space-y-2">
 @csrf
 <div class="max-h-44 space-y-1 overflow-y-auto pr-1">
 @foreach($allowedCompanies as $c)
 <label class="flex cursor-pointer items-center gap-2 rounded-lg px-2 py-2 text-xs text-white ring-1 ring-transparent hover:ring-white/50 {{ $activeCompanyId === $c->id ? 'ring-white' : '' }}">
 <input type="radio" name="company_id" value="{{ $c->id }}" class="text-white focus:ring-white" @checked($activeCompanyId === $c->id)>
 <span class="min-w-0 truncate">
 <span class="font-bold">{{ $c->code ?? 'COMP' }}</span>
 <span class="text-white/75">- {{ $c->name }}</span>
 </span>
 </label>
 @endforeach
 </div>
 <button type="submit" class="w-full rounded-lg bg-white px-3 py-2 text-xs font-extrabold text-maroon-900">Simpan</button>
 </form>
 </div>
 @endif
 </div>

 <div class="arca-sidebar-card rounded-2xl border border-white/25 bg-maroon-900 p-3" x-data="{ openSwitch:false }">
 <div class="flex items-start gap-3">
 <div class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-white text-maroon-900">
 {!! $icon('map', 'h-4 w-4') !!}
 </div>
 <div class="min-w-0 flex-1">
 <div class="text-[10px] font-bold uppercase tracking-wide text-white/70">Site</div>
 <div class="mt-0.5 truncate text-xs font-semibold text-white">{{ $siteLabel }}</div>
 </div>
 @if($canSwitchSite)
 <button type="button"
 @click="openSwitch = !openSwitch"
 class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-white text-maroon-900"
 aria-label="Ganti site">
 {!! $icon('switch', 'h-4 w-4') !!}
 </button>
 @endif
 </div>

 @if($canSwitchSite)
 <div x-cloak x-show="openSwitch" x-transition class="mt-3 border-t border-white/25 pt-3">
 <form action="{{ route('site.switch') }}" method="POST" class="space-y-2">
 @csrf
 <div class="max-h-44 space-y-1 overflow-y-auto pr-1">
 @foreach($allowedSites as $s)
 <label class="flex cursor-pointer items-center gap-2 rounded-lg px-2 py-2 text-xs text-white ring-1 ring-transparent hover:ring-white/50 {{ $activeSiteId === $s->id ? 'ring-white' : '' }}">
 <input type="radio" name="site_id" value="{{ $s->id }}" class="text-white focus:ring-white" @checked($activeSiteId === $s->id)>
 <span class="min-w-0 truncate">
 <span class="font-bold">{{ $s->code }}</span>
 <span class="text-white/75">- {{ $s->name }}</span>
 </span>
 </label>
 @endforeach
 </div>
 <button type="submit" class="w-full rounded-lg bg-white px-3 py-2 text-xs font-extrabold text-maroon-900">Simpan</button>
 </form>
 </div>
 @endif
 </div>
 </div>

 <nav class="arca-nav-root space-y-1">
 <a href="{{ route('dashboard') }}"
 class="{{ $navItemClass }} {{ request()->routeIs('dashboard') ? 'bg-white text-maroon-900' : 'text-white hover:ring-1 hover:ring-white/40' }}"
 :class="sidebarExpanded ? 'justify-start gap-3' : 'justify-center'">
 <span class="{{ $iconBoxBase }} {{ request()->routeIs('dashboard') ? 'border-maroon-900 bg-white text-maroon-900' : 'border-white/30 text-white group-hover:border-white' }}">
 {!! $icon('dashboard') !!}
 </span>
 <span class="truncate text-sm font-semibold" x-show="sidebarExpanded" x-transition.opacity.duration.150ms>Dashboard</span>
 </a>

 <a href="{{ route('powerbi.sites') }}"
 class="{{ $navItemClass }} {{ request()->routeIs('powerbi.*') ? 'bg-white text-maroon-900' : 'text-white hover:ring-1 hover:ring-white/40' }}"
 :class="sidebarExpanded ? 'justify-start gap-3' : 'justify-center'">
 <span class="{{ $iconBoxBase }} {{ request()->routeIs('powerbi.*') ? 'border-maroon-900 bg-white text-maroon-900' : 'border-white/30 text-white group-hover:border-white' }}">
 {!! $icon('chart') !!}
 </span>
 <span class="min-w-0 flex-1" x-show="sidebarExpanded" x-transition.opacity.duration.150ms>
 <span class="block truncate text-sm font-semibold">Dashboards</span>
 @if($activeSite)
 <span class="block truncate text-[11px] opacity-75">Site {{ $activeSite->code }}</span>
 @endif
 </span>
 </a>

 @if($activeSite)
 <a href="{{ route('powerbi.site.reports', $activeSite) }}"
 class="ml-12 block rounded-lg px-3 py-2 text-xs font-semibold text-white/85 hover:ring-1 hover:ring-white/40"
 x-show="sidebarExpanded"
 x-transition.opacity.duration.150ms>
 Buka {{ $activeSite->code }}
 </a>
 @endif

 @if($canSeeAdmin)
 <div class="arca-section-heading px-2 text-[10px] font-extrabold uppercase tracking-[0.18em] text-white/60" x-show="sidebarExpanded" x-transition.opacity.duration.150ms>Admin</div>

 <a href="{{ route('admin.companies.index') }}"
 class="{{ $navItemClass }} {{ request()->routeIs('admin.companies.*') ? 'bg-white text-maroon-900' : 'text-white hover:ring-1 hover:ring-white/40' }}"
 :class="sidebarExpanded ? 'justify-start gap-3' : 'justify-center'">
 <span class="{{ $iconBoxBase }} {{ request()->routeIs('admin.companies.*') ? 'border-maroon-900 bg-white text-maroon-900' : 'border-white/30 text-white group-hover:border-white' }}">{!! $icon('building') !!}</span>
 <span class="truncate text-sm font-semibold" x-show="sidebarExpanded" x-transition.opacity.duration.150ms>Companies</span>
 </a>

 <a href="{{ route('admin.sites.index') }}"
 class="{{ $navItemClass }} {{ request()->routeIs('admin.sites.*') ? 'bg-white text-maroon-900' : 'text-white hover:ring-1 hover:ring-white/40' }}"
 :class="sidebarExpanded ? 'justify-start gap-3' : 'justify-center'">
 <span class="{{ $iconBoxBase }} {{ request()->routeIs('admin.sites.*') ? 'border-maroon-900 bg-white text-maroon-900' : 'border-white/30 text-white group-hover:border-white' }}">{!! $icon('map') !!}</span>
 <span class="truncate text-sm font-semibold" x-show="sidebarExpanded" x-transition.opacity.duration.150ms>Sites</span>
 </a>

 <a href="{{ route('admin.powerbi.index') }}"
 class="{{ $navItemClass }} {{ request()->routeIs('admin.powerbi.*') ? 'bg-white text-maroon-900' : 'text-white hover:ring-1 hover:ring-white/40' }}"
 :class="sidebarExpanded ? 'justify-start gap-3' : 'justify-center'">
 <span class="{{ $iconBoxBase }} {{ request()->routeIs('admin.powerbi.*') ? 'border-maroon-900 bg-white text-maroon-900' : 'border-white/30 text-white group-hover:border-white' }}">{!! $icon('briefcase') !!}</span>
 <span class="truncate text-sm font-semibold" x-show="sidebarExpanded" x-transition.opacity.duration.150ms>Power BI Admin</span>
 </a>

 <a href="{{ route('admin.divisions.index') }}"
 class="{{ $navItemClass }} {{ request()->routeIs('admin.divisions.*') ? 'bg-white text-maroon-900' : 'text-white hover:ring-1 hover:ring-white/40' }}"
 :class="sidebarExpanded ? 'justify-start gap-3' : 'justify-center'">
 <span class="{{ $iconBoxBase }} {{ request()->routeIs('admin.divisions.*') ? 'border-maroon-900 bg-white text-maroon-900' : 'border-white/30 text-white group-hover:border-white' }}">{!! $icon('layers') !!}</span>
 <span class="truncate text-sm font-semibold" x-show="sidebarExpanded" x-transition.opacity.duration.150ms>Divisions</span>
 </a>

 <a href="{{ route('admin.users.index') }}"
 class="{{ $navItemClass }} {{ request()->routeIs('admin.users.*') ? 'bg-white text-maroon-900' : 'text-white hover:ring-1 hover:ring-white/40' }}"
 :class="sidebarExpanded ? 'justify-start gap-3' : 'justify-center'">
 <span class="{{ $iconBoxBase }} {{ request()->routeIs('admin.users.*') ? 'border-maroon-900 bg-white text-maroon-900' : 'border-white/30 text-white group-hover:border-white' }}">{!! $icon('users') !!}</span>
 <span class="truncate text-sm font-semibold" x-show="sidebarExpanded" x-transition.opacity.duration.150ms>Users</span>
 </a>
 @endif

 @if($canSeeSecurity)
 <div class="arca-section-heading px-2 text-[10px] font-extrabold uppercase tracking-[0.18em] text-white/60" x-show="sidebarExpanded" x-transition.opacity.duration.150ms>Security</div>

 <a href="{{ route('admin.audit.index') }}"
 class="{{ $navItemClass }} {{ request()->routeIs('admin.audit.index') ? 'bg-white text-maroon-900' : 'text-white hover:ring-1 hover:ring-white/40' }}"
 :class="sidebarExpanded ? 'justify-start gap-3' : 'justify-center'">
 <span class="{{ $iconBoxBase }} {{ request()->routeIs('admin.audit.index') ? 'border-maroon-900 bg-white text-maroon-900' : 'border-white/30 text-white group-hover:border-white' }}">{!! $icon('shield') !!}</span>
 <span class="truncate text-sm font-semibold" x-show="sidebarExpanded" x-transition.opacity.duration.150ms>Audit Log</span>
 </a>

 <a href="{{ route('admin.audit.user', $u?->id) }}"
 class="{{ $navItemClass }} {{ request()->routeIs('admin.audit.user') ? 'bg-white text-maroon-900' : 'text-white hover:ring-1 hover:ring-white/40' }}"
 :class="sidebarExpanded ? 'justify-start gap-3' : 'justify-center'">
 <span class="{{ $iconBoxBase }} {{ request()->routeIs('admin.audit.user') ? 'border-maroon-900 bg-white text-maroon-900' : 'border-white/30 text-white group-hover:border-white' }}">{!! $icon('user') !!}</span>
 <span class="truncate text-sm font-semibold" x-show="sidebarExpanded" x-transition.opacity.duration.150ms>Audit User</span>
 </a>
 @endif
 </nav>
 </div>

 <div class="border-t border-white/25 p-3">
 <form method="POST" action="{{ route('logout') }}">
 @csrf
 <button type="submit"
 class="flex w-full items-center rounded-xl bg-white px-2.5 py-2.5 font-bold text-maroon-900 transition"
 :class="sidebarExpanded ? 'justify-start gap-3' : 'justify-center'">
 <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl border border-maroon-900 bg-white text-maroon-900">
 {!! $icon('logout') !!}
 </span>
 <span class="truncate text-sm" x-show="sidebarExpanded" x-transition.opacity.duration.150ms>Keluar</span>
 </button>
 </form>
 </div>
</aside>
