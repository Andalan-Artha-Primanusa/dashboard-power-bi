<aside class="w-72 bg-maroon-700 text-white flex flex-col h-screen">
  {{-- HEADER --}}
  <div class="flex items-center justify-between h-16 px-4 border-b border-maroon-600 bg-maroon-800">
    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
      <x-application-logo class="h-8 w-auto text-gold-500" />
      <span class="font-bold text-lg text-gold-400">BISA ERP</span>
    </a>
    @if(($mobile ?? false) === true)
      <button @click="$root.sidebarOpen=false" class="p-2 rounded text-gold-300 hover:text-gold-100 lg:hidden" aria-label="Close sidebar">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    @endif
  </div>

  {{-- NAV --}}
  <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-2">
    <a href="{{ route('dashboard') }}"
       class="flex items-center gap-2 px-3 py-2 rounded-md transition
              {{ request()->routeIs('dashboard') ? 'bg-gold-500 text-maroon-900 font-semibold' : 'hover:bg-maroon-600 text-white' }}">
      📊 Dashboard
    </a>

    <a href="{{ route('powerbi.index') }}"
       class="flex items-center gap-2 px-3 py-2 rounded-md transition
              {{ request()->routeIs('powerbi.*') ? 'bg-gold-500 text-maroon-900 font-semibold' : 'hover:bg-maroon-600 text-white' }}">
      📈 Power BI
    </a>

    @php($u = Auth::user())

    {{-- Users (GM & Super Admin) : pakai Gate atau fallback cek role --}}
    @if($u && ($u->can('manage-users') || in_array($u->role ?? 'user', ['gm','super_admin'], true)))
      <a href="{{ route('admin.users.index') }}"
         class="flex items-center gap-2 px-3 py-2 rounded-md transition
                {{ request()->routeIs('admin.users.*') ? 'bg-gold-500 text-maroon-900 font-semibold' : 'hover:bg-maroon-600 text-white' }}">
        👥 Users
      </a>
    @endif

    {{-- Audit (Super Admin only) : pakai Gate atau fallback cek role --}}
    @if($u && ($u->can('view-audit') || ($u->role ?? 'user') === 'super_admin'))
      <a href="{{ route('admin.audit.index') }}"
         class="flex items-center gap-2 px-3 py-2 rounded-md transition
                {{ request()->routeIs('admin.audit.*') ? 'bg-gold-500 text-maroon-900 font-semibold' : 'hover:bg-maroon-600 text-white' }}">
        📜 Audit Log
      </a>
    @endif
  </nav>

  {{-- USER --}}
  <div class="border-t border-maroon-600 bg-maroon-800 px-4 py-3">
    <div class="flex items-center justify-between">
      <div class="min-w-0">
        <div class="font-medium text-gold-400 truncate">{{ Auth::user()->name ?? 'User' }}</div>
        <div class="text-sm text-gold-200 truncate">{{ Auth::user()->email ?? '' }}</div>
      </div>
      <x-dropdown align="right" width="48">
        <x-slot name="trigger">
          <button class="text-gold-400 hover:text-gold-200 focus:outline-none">
            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.198l3.71-3.97a.75.75 0 111.08 1.04l-4.24 4.54a.75.75 0 01-1.08 0l-4.24-4.54a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
            </svg>
          </button>
        </x-slot>
        <x-slot name="content">
          <x-dropdown-link :href="route('profile.edit')">
            {{ __('Profile') }}
          </x-dropdown-link>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <x-dropdown-link :href="route('logout')"
                onclick="event.preventDefault(); this.closest('form').submit();">
              {{ __('Log Out') }}
            </x-dropdown-link>
          </form>
        </x-slot>
      </x-dropdown>
    </div>
  </div>
</aside>
