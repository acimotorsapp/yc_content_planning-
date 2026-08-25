<nav class="w-64 bg-white border-r border-gray-200 text-gray-900 flex flex-col h-screen transition-all duration-300 z-40 relative shrink-0 shadow-xs">
    <!-- Logo -->
    <div class="flex items-center justify-center h-20 border-b border-gray-100">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 group">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 text-white flex items-center justify-center text-xl font-black shadow-md shadow-blue-500/20 group-hover:scale-105 transition-transform">
                Y
            </div>
            <div class="flex flex-col">
                <span class="text-base font-bold tracking-wider text-gray-900 group-hover:text-blue-600 transition-colors leading-tight">YC Content</span>
                <span class="text-[11px] font-semibold tracking-widest text-gray-400 uppercase group-hover:text-gray-600 transition-colors mt-0.5">Planning</span>
            </div>
        </a>
    </div>

    <!-- Navigation Links -->
    <div class="flex-1 overflow-y-auto py-6 px-4 space-y-1.5">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600 font-bold shadow-xs' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 font-medium' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span>Dashboard</span>
        </a>

        <!-- New Event Button in Sidebar (Opens modal directly without page change) -->
        <button type="button" @click="showCreateModal = true" class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 text-gray-600 hover:bg-gray-50 hover:text-gray-900 font-medium focus:outline-none text-left cursor-pointer group">
            <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-900 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>New Event</span>
        </button>

        <a href="{{ route('events.my') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('events.my') ? 'bg-blue-50 text-blue-600 font-bold shadow-xs' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 font-medium' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('events.my') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            <span>My Events</span>
        </a>

        @if(auth()->check() && auth()->user()->role === 'super_admin')
            <div class="pt-5 pb-2">
                <p class="px-4 text-[10px] font-extrabold tracking-widest text-gray-400 uppercase">Admin Hub</p>
            </div>
            
            <a href="{{ route('admin.events.product') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.events.product') ? 'bg-blue-50 text-blue-600 font-bold shadow-xs' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 font-medium' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.events.product') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                <span>Product Events</span>
            </a>

            <a href="{{ route('admin.events.digital') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.events.digital') ? 'bg-blue-50 text-blue-600 font-bold shadow-xs' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 font-medium' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.events.digital') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                <span>Digital Events</span>
            </a>

            <a href="{{ route('admin.events.global') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.events.global') ? 'bg-blue-50 text-blue-600 font-bold shadow-xs' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 font-medium' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.events.global') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Global Events</span>
            </a>
            
            <a href="{{ route('admin.master_data.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.master_data.*') ? 'bg-blue-50 text-blue-600 font-bold shadow-xs' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 font-medium' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.master_data.*') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                <span>Manage Categories</span>
            </a>
            
            <a href="{{ route('admin.users.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'bg-blue-50 text-blue-600 font-bold shadow-xs' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 font-medium' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.users.*') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <span>User Management</span>
            </a>

            <a href="{{ route('admin.settings') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.settings') ? 'bg-blue-50 text-blue-600 font-bold shadow-xs' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 font-medium' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.settings') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span>System Settings</span>
            </a>
        @endif
    </div>

    <!-- Bottom User Settings -->
    <div class="border-t border-gray-100 p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3 truncate">
                <div class="w-9 h-9 rounded-xl bg-gray-100 border border-gray-200 flex items-center justify-center text-sm font-bold text-gray-700">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="truncate">
                    <div class="text-sm font-bold text-gray-900 truncate">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-gray-400 capitalize">{{ str_replace('_', ' ', Auth::user()->role) }}</div>
                </div>
            </div>
            
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="p-2 rounded-xl text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors" title="Log Out">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </button>
            </form>
        </div>
    </div>
</nav>
