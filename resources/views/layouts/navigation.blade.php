<nav x-data="{ open: false }" class="sticky top-0 z-50 site-card bg-opacity-80 backdrop-blur-xl border-b border-white/10 shadow-2xl">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20 items-center">
            <!-- Brand Logo -->
            <div class="shrink-0 flex items-center">
                <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                    <span class="font-serif text-2xl font-bold tracking-widest text-white group-hover:text-[var(--theme-primary)] transition-colors">
                        {{ $siteSettings['logo_text'] ?? 'Middukhera' }}
                    </span>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded tracking-widest bg-theme-primary text-black">
                        {{ $siteSettings['logo_sub'] ?? 'Production' }}
                    </span>
                </a>
            </div>

            <!-- Desktop Navigation Links -->
            <div class="hidden md:flex space-x-1 lg:space-x-2 items-center">
                <a href="{{ route('home') }}" class="px-3.5 py-2 text-sm font-medium rounded-full transition-all duration-200 {{ request()->routeIs('home') ? 'bg-white/10 text-white font-semibold shadow-inner' : 'text-zinc-300 hover:text-white hover:bg-white/5' }}">
                    Home
                </a>
                <a href="{{ route('about') }}" class="px-3.5 py-2 text-sm font-medium rounded-full transition-all duration-200 {{ request()->routeIs('about') ? 'bg-white/10 text-white font-semibold shadow-inner' : 'text-zinc-300 hover:text-white hover:bg-white/5' }}">
                    About Studio
                </a>
                <a href="{{ route('gallery') }}" class="px-3.5 py-2 text-sm font-medium rounded-full transition-all duration-200 {{ request()->routeIs('gallery') ? 'bg-white/10 text-white font-semibold shadow-inner' : 'text-zinc-300 hover:text-white hover:bg-white/5' }}">
                    Gallery
                </a>
                <a href="{{ route('blog') }}" class="px-3.5 py-2 text-sm font-medium rounded-full transition-all duration-200 {{ request()->routeIs('blog*') ? 'bg-white/10 text-white font-semibold shadow-inner' : 'text-zinc-300 hover:text-white hover:bg-white/5' }}">
                    Journal
                </a>
                <a href="{{ route('contact') }}" class="px-3.5 py-2 text-sm font-medium rounded-full transition-all duration-200 {{ request()->routeIs('contact') ? 'bg-white/10 text-white font-semibold shadow-inner' : 'text-zinc-300 hover:text-white hover:bg-white/5' }}">
                    Contact
                </a>

                @auth
                    @if(auth()->user()->isVendor())
                        <a href="{{ route('vendor.dashboard') }}" class="px-3.5 py-2 text-sm font-medium rounded-full transition-all duration-200 {{ request()->routeIs('vendor.dashboard') ? 'bg-amber-500/20 text-amber-300' : 'text-amber-400 hover:bg-amber-500/10' }}">
                            Photographer Hub
                        </a>
                    @endif
                    @if(auth()->user()->isClient())
                        <a href="{{ route('client.dashboard') }}" class="px-3.5 py-2 text-sm font-medium rounded-full transition-all duration-200 {{ request()->routeIs('client.dashboard') ? 'bg-theme-primary/20 text-theme-primary' : 'text-theme-primary hover:bg-white/5' }}">
                            My Bookings
                        </a>
                    @endif
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="px-3.5 py-2 text-sm font-medium rounded-full transition-all duration-200 {{ request()->routeIs('admin.*') ? 'bg-purple-500/20 text-purple-300' : 'text-purple-400 hover:bg-purple-500/10' }}">
                            Super Admin
                        </a>
                    @endif
                @endauth
            </div>

            <!-- Desktop Auth / CTA -->
            <div class="hidden md:flex items-center space-x-3">
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-2 px-4 py-2 border border-white/10 text-sm font-medium rounded-full text-white bg-white/5 hover:bg-white/10 hover:border-white/20 focus:outline-none transition">
                                <i data-lucide="user" class="w-4 h-4 text-theme-primary"></i>
                                <span>{{ Auth::user()->name }}</span>
                                <i data-lucide="chevron-down" class="w-3.5 h-3.5 opacity-60"></i>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="site-card rounded-xl border border-white/10 py-1 shadow-2xl divide-y divide-white/5">
                                <div class="px-4 py-2 text-xs text-zinc-400">
                                    Signed in as <strong class="text-white block truncate">{{ Auth::user()->email }}</strong>
                                </div>
                                <div class="py-1">
                                    <x-dropdown-link :href="route('dashboard')" class="text-zinc-300 hover:text-white hover:bg-white/5">
                                        Dashboard
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('profile.edit')" class="text-zinc-300 hover:text-white hover:bg-white/5">
                                        Account Profile
                                    </x-dropdown-link>
                                </div>
                                <div class="py-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="text-rose-400 hover:text-rose-300 hover:bg-rose-500/10">
                                            Log Out
                                        </x-dropdown-link>
                                    </form>
                                </div>
                            </div>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}" class="px-3.5 py-2 text-sm font-medium text-zinc-300 hover:text-white transition-colors">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-semibold rounded-full border border-white/20 text-white hover:border-[var(--theme-primary)] hover:text-theme-primary transition-all duration-200">
                        Sign Up
                    </a>
                    <a href="{{ route('home') }}#packages" class="px-5 py-2.5 text-xs font-bold uppercase tracking-wider rounded-full btn-gold-dynamic shadow-lg shadow-[var(--theme-primary)]/20">
                        Book Session
                    </a>
                @endauth
            </div>

            <!-- Mobile Hamburger -->
            <div class="flex items-center md:hidden">
                <button @click="open = !open" class="p-2 rounded-xl text-zinc-300 hover:text-white hover:bg-white/5 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Drawer -->
    <div :class="{'block': open, 'hidden': !open}" class="hidden md:hidden site-card border-b border-white/10 px-4 pt-2 pb-6 space-y-2">
        <a href="{{ route('home') }}" class="block px-3 py-2 text-base font-medium rounded-lg text-white hover:bg-white/5">Home</a>
        <a href="{{ route('about') }}" class="block px-3 py-2 text-base font-medium rounded-lg text-zinc-300 hover:bg-white/5">About Studio</a>
        <a href="{{ route('gallery') }}" class="block px-3 py-2 text-base font-medium rounded-lg text-zinc-300 hover:bg-white/5">Gallery</a>
        <a href="{{ route('blog') }}" class="block px-3 py-2 text-base font-medium rounded-lg text-zinc-300 hover:bg-white/5">Journal</a>
        <a href="{{ route('contact') }}" class="block px-3 py-2 text-base font-medium rounded-lg text-zinc-300 hover:bg-white/5">Contact</a>

        @auth
            @if(auth()->user()->isVendor())
                <a href="{{ route('vendor.dashboard') }}" class="block px-3 py-2 text-base font-medium rounded-lg text-amber-400 hover:bg-white/5">Photographer Hub</a>
            @endif
            @if(auth()->user()->isClient())
                <a href="{{ route('client.dashboard') }}" class="block px-3 py-2 text-base font-medium rounded-lg text-theme-primary hover:bg-white/5">My Bookings</a>
            @endif
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 text-base font-medium rounded-lg text-purple-400 hover:bg-white/5">Super Admin</a>
            @endif
            
            <div class="pt-4 border-t border-white/10 space-y-2">
                <div class="text-xs text-zinc-400 px-3">Signed in as <strong class="text-white">{{ Auth::user()->name }}</strong></div>
                <a href="{{ route('profile.edit') }}" class="block px-3 py-2 text-sm text-zinc-300 hover:bg-white/5 rounded-lg">Account Profile</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 text-sm text-rose-400 hover:bg-rose-500/10 rounded-lg">Log Out</button>
                </form>
            </div>
        @else
            <div class="pt-4 border-t border-white/10 flex flex-col gap-2">
                <a href="{{ route('login') }}" class="block text-center py-2.5 text-sm font-medium text-white border border-white/10 rounded-xl hover:bg-white/5">Sign In</a>
                <a href="{{ route('register') }}" class="block text-center py-2.5 text-sm font-bold uppercase tracking-wider rounded-xl btn-gold-dynamic">Create Account / Sign Up</a>
            </div>
        @endauth
    </div>
</nav>
