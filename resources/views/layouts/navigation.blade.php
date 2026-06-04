<!-- Mobile overlay -->
<div x-show="sidebarOpen" class="fixed inset-0 bg-black/40 z-20 lg:hidden" @click="sidebarOpen = false" x-transition.opacity></div>

<!-- Sidebar -->
<aside :class="[
            sidebarOpen ? 'translate-x-0' : '-translate-x-full',
            sidebarMinimized ? 'w-20' : 'w-64'
       ]"
       class="fixed top-0 left-0 h-full bg-white border-r border-slate-200 z-30
              flex flex-col shadow-sm lg:translate-x-0 transition-all duration-300">

    <!-- Toggle Button (Edge) -->
    <button @click="sidebarMinimized = !sidebarMinimized"
            class="hidden lg:flex items-center justify-center absolute top-1/2 -translate-y-1/2 -right-4 w-4 h-16 bg-white border border-l-0 border-slate-200 rounded-r-xl text-slate-300 hover:text-brand-600 hover:bg-slate-50 focus:outline-none z-40 transition-colors cursor-pointer">
        <svg x-show="!sidebarMinimized" class="w-3.5 h-3.5 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
        <svg x-show="sidebarMinimized" class="w-3.5 h-3.5 mr-0.5 hidden" :class="sidebarMinimized ? '!block' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
    </button>

    <!-- Logo -->
    <div class="h-16 flex items-center justify-between px-4 sm:px-6 border-b border-slate-100 relative">
        <div class="flex items-center gap-3 overflow-hidden" :class="sidebarMinimized ? 'justify-center w-full' : ''">
            <div class="w-8 h-8 bg-brand-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11
                             0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11
                             0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <span x-show="!sidebarMinimized" x-transition.opacity.duration.300ms class="font-bold text-slate-800 text-lg tracking-tight whitespace-nowrap">CashFlow</span>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 p-3 sm:p-4 space-y-5 overflow-y-auto overflow-x-hidden">

        @php
            $navClasses = function($active) {
                $base = 'flex items-center gap-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150 relative group';
                return $active ? "$base bg-brand-600 text-white shadow-sm" : "$base text-slate-500 hover:bg-slate-100 hover:text-slate-800";
            };
        @endphp

        <!-- Overview -->
        <div>
            <div x-show="!sidebarMinimized" x-transition.opacity class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest px-4 mb-2 whitespace-nowrap">Overview</div>
            <div x-show="sidebarMinimized" class="h-4"></div>
            <div class="space-y-1">
                <a href="{{ route('dashboard') }}" class="{{ $navClasses(request()->routeIs('dashboard')) }}" :class="sidebarMinimized ? 'justify-center px-0' : 'px-4'">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span x-show="!sidebarMinimized" class="whitespace-nowrap">Dashboard</span>
                    <div x-show="sidebarMinimized" class="absolute left-full ml-4 px-2.5 py-1.5 bg-slate-800 text-white text-xs rounded opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all whitespace-nowrap z-50 pointer-events-none">Dashboard</div>
                </a>
            </div>
        </div>

        <!-- Money Flow -->
        <div>
            <div x-show="!sidebarMinimized" x-transition.opacity class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest px-4 mb-2 whitespace-nowrap">Money Flow</div>
            <div x-show="sidebarMinimized" class="w-8 mx-auto h-[1px] bg-slate-200 my-4"></div>
            <div class="space-y-1">
                <a href="{{ route('transactions.index') }}" class="{{ $navClasses(request()->routeIs('transactions.*')) }}" :class="sidebarMinimized ? 'justify-center px-0' : 'px-4'">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    <span x-show="!sidebarMinimized" class="whitespace-nowrap">Transactions</span>
                    <div x-show="sidebarMinimized" class="absolute left-full ml-4 px-2.5 py-1.5 bg-slate-800 text-white text-xs rounded opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all whitespace-nowrap z-50 pointer-events-none">Transactions</div>
                </a>
                <a href="{{ route('transfers.index') }}" class="{{ $navClasses(request()->routeIs('transfers.*')) }}" :class="sidebarMinimized ? 'justify-center px-0' : 'px-4'">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    <span x-show="!sidebarMinimized" class="whitespace-nowrap">Transfers</span>
                    <div x-show="sidebarMinimized" class="absolute left-full ml-4 px-2.5 py-1.5 bg-slate-800 text-white text-xs rounded opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all whitespace-nowrap z-50 pointer-events-none">Transfers</div>
                </a>
            </div>
        </div>

        <!-- Planning -->
        <div>
            <div x-show="!sidebarMinimized" x-transition.opacity class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest px-4 mb-2 whitespace-nowrap">Planning</div>
            <div x-show="sidebarMinimized" class="w-8 mx-auto h-[1px] bg-slate-200 my-4"></div>
            <div class="space-y-1">
                <a href="{{ route('wishlists.index') }}" class="{{ $navClasses(request()->routeIs('wishlists.*')) }}" :class="sidebarMinimized ? 'justify-center px-0' : 'px-4'">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    <span x-show="!sidebarMinimized" class="whitespace-nowrap">Wishlist</span>
                    <div x-show="sidebarMinimized" class="absolute left-full ml-4 px-2.5 py-1.5 bg-slate-800 text-white text-xs rounded opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all whitespace-nowrap z-50 pointer-events-none">Wishlist</div>
                </a>
                <a href="{{ route('debts.index') }}" class="{{ $navClasses(request()->routeIs('debts.*')) }}" :class="sidebarMinimized ? 'justify-center px-0' : 'px-4'">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span x-show="!sidebarMinimized" class="whitespace-nowrap">Debts & Receivables</span>
                    <div x-show="sidebarMinimized" class="absolute left-full ml-4 px-2.5 py-1.5 bg-slate-800 text-white text-xs rounded opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all whitespace-nowrap z-50 pointer-events-none">Debts & Receivables</div>
                </a>
            </div>
        </div>

        <!-- Settings -->
        <div>
            <div x-show="!sidebarMinimized" x-transition.opacity class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest px-4 mb-2 whitespace-nowrap">Settings</div>
            <div x-show="sidebarMinimized" class="w-8 mx-auto h-[1px] bg-slate-200 my-4"></div>
            <div class="space-y-1">
                <a href="{{ route('categories.index') }}" class="{{ $navClasses(request()->routeIs('categories.*')) }}" :class="sidebarMinimized ? 'justify-center px-0' : 'px-4'">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    <span x-show="!sidebarMinimized" class="whitespace-nowrap">Categories</span>
                    <div x-show="sidebarMinimized" class="absolute left-full ml-4 px-2.5 py-1.5 bg-slate-800 text-white text-xs rounded opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all whitespace-nowrap z-50 pointer-events-none">Categories</div>
                </a>
                <a href="{{ route('payment-methods.index') }}" class="{{ $navClasses(request()->routeIs('payment-methods.*')) }}" :class="sidebarMinimized ? 'justify-center px-0' : 'px-4'">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    <span x-show="!sidebarMinimized" class="whitespace-nowrap">Payment Methods</span>
                    <div x-show="sidebarMinimized" class="absolute left-full ml-4 px-2.5 py-1.5 bg-slate-800 text-white text-xs rounded opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all whitespace-nowrap z-50 pointer-events-none">Payment Methods</div>
                </a>
            </div>
        </div>

    </nav>

    <!-- User -->
    <div class="p-3 sm:p-4 border-t border-slate-100">

        <div class="flex items-center gap-3 px-2 mb-3" :class="sidebarMinimized ? 'justify-center px-0' : ''">
            <div class="w-8 h-8 rounded-full bg-brand-100 flex items-center justify-center text-brand-700 font-bold text-sm flex-shrink-0 relative group cursor-pointer">
                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                <div x-show="sidebarMinimized" class="absolute left-full ml-4 px-2.5 py-1.5 bg-slate-800 text-white text-xs rounded opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all whitespace-nowrap z-50 pointer-events-none">{{ Auth::user()->name }}</div>
            </div>
            <div x-show="!sidebarMinimized" class="min-w-0">
                <p class="text-sm font-semibold text-slate-700 truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-slate-400 truncate">CashFlow App</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-2 py-2 text-sm text-red-500 hover:bg-red-50 rounded-xl transition-colors relative group" :class="sidebarMinimized ? 'justify-center px-0' : 'px-4'">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span x-show="!sidebarMinimized" class="whitespace-nowrap">Logout</span>
                <div x-show="sidebarMinimized" class="absolute left-full ml-4 px-2.5 py-1.5 bg-red-600 text-white text-xs rounded opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all whitespace-nowrap z-50 pointer-events-none">Logout</div>
            </button>
        </form>
    </div>
</aside>
