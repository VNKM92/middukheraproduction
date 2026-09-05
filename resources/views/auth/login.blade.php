@extends('layouts.app')

@section('content')
<div class="relative min-h-[calc(100vh-80px)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 overflow-hidden">
    <!-- Ambient Backdrop Effects -->
    <div class="absolute top-1/3 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[550px] h-[550px] bg-[var(--theme-primary)]/10 rounded-full blur-[140px] pointer-events-none"></div>
    <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-purple-500/10 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10">
        <!-- Brand Header / Badge -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full border border-[var(--theme-primary)]/30 bg-[var(--theme-primary)]/10 text-theme-primary text-xs font-semibold tracking-wider uppercase mb-3 shadow-inner">
                <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                <span>Client Portal Access</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-serif font-bold text-white tracking-tight">
                Welcome Back
            </h1>
            <p class="text-sm text-zinc-400 mt-2">
                Sign in to manage your bookings, review contracts, and view your private photoshoot galleries.
            </p>
        </div>

        <!-- Login Card -->
        <div class="site-card rounded-3xl border border-white/10 p-7 sm:p-9 shadow-2xl backdrop-blur-xl relative">
            
            <!-- Session Status Flash -->
            @if (session('status'))
                <div class="mb-5 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4 shrink-0 text-emerald-400"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <!-- Global / Validation Errors -->
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs space-y-1">
                    <div class="font-bold flex items-center gap-2 text-rose-200">
                        <i data-lucide="alert-circle" class="w-4 h-4 text-rose-400"></i>
                        <span>Authentication failed:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 pl-1 pt-1 text-zinc-300">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5" x-data="{ showPass: false }">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-zinc-300 mb-2">
                        Email Address
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                            <i data-lucide="mail" class="w-4 h-4"></i>
                        </div>
                        <input id="email"
                               type="email"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               autofocus
                               autocomplete="username"
                               placeholder="you@example.com"
                               class="w-full pl-10 pr-4 py-3 text-sm bg-black/40 border {{ $errors->has('email') ? 'border-rose-500/80 focus:ring-rose-500' : 'border-white/10 focus:border-[var(--theme-primary)] focus:ring-[var(--theme-primary)]' }} rounded-xl text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-opacity-40 transition-all duration-200" />
                    </div>
                    @error('email')
                        <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="text-xs font-semibold uppercase tracking-wider text-zinc-300">
                            Password
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs text-theme-primary hover:underline font-medium">
                                Forgot password?
                            </a>
                        @endif
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </div>
                        <input id="password"
                               :type="showPass ? 'text' : 'password'"
                               name="password"
                               required
                               autocomplete="current-password"
                               placeholder="Enter your password"
                               class="w-full pl-10 pr-10 py-3 text-sm bg-black/40 border {{ $errors->has('password') ? 'border-rose-500/80 focus:ring-rose-500' : 'border-white/10 focus:border-[var(--theme-primary)] focus:ring-[var(--theme-primary)]' }} rounded-xl text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-opacity-40 transition-all duration-200" />
                        <button type="button"
                                @click="showPass = !showPass"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-zinc-400 hover:text-white transition">
                            <i data-lucide="eye" x-show="!showPass" class="w-4 h-4"></i>
                            <i data-lucide="eye-off" x-show="showPass" class="w-4 h-4" style="display:none;"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me Checkbox -->
                <div class="flex items-center justify-between pt-1">
                    <label for="remember_me" class="inline-flex items-center cursor-pointer">
                        <input id="remember_me"
                               type="checkbox"
                               name="remember"
                               class="w-4 h-4 rounded bg-black/40 border-white/20 text-[var(--theme-primary)] focus:ring-[var(--theme-primary)] focus:ring-offset-0 focus:ring-2 transition cursor-pointer">
                        <span class="ms-2.5 text-xs text-zinc-300 select-none">Remember this device</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        class="w-full py-3.5 px-6 rounded-xl font-bold text-xs sm:text-sm uppercase tracking-wider btn-gold-dynamic shadow-xl shadow-[var(--theme-primary)]/20 flex items-center justify-center gap-2 mt-2">
                    <i data-lucide="log-in" class="w-4 h-4"></i>
                    <span>Sign In</span>
                </button>
            </form>

            <!-- Divider -->
            <div class="relative my-6 text-center">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-white/10"></div>
                </div>
                <div class="relative inline-block px-4 bg-[#12101b] text-xs uppercase tracking-widest text-zinc-500 font-semibold">
                    New to the Studio?
                </div>
            </div>

            <!-- Sign Up Link -->
            <div class="text-center">
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 w-full py-3 px-4 rounded-xl text-sm font-semibold text-zinc-200 border border-white/10 hover:border-[var(--theme-primary)]/50 hover:bg-white/5 transition-all duration-200">
                    <i data-lucide="user-plus" class="w-4 h-4 text-theme-primary"></i>
                    <span>Create Client Account / Sign Up</span>
                </a>
            </div>

            <!-- Security Trust Footnote -->
            <div class="mt-8 pt-6 border-t border-white/5 flex flex-wrap items-center justify-center gap-4 text-[11px] text-zinc-400">
                <div class="flex items-center gap-1.5">
                    <i data-lucide="shield" class="w-3.5 h-3.5 text-emerald-400"></i>
                    <span>256-Bit SSL Encrypted</span>
                </div>
                <span class="text-zinc-600">&bull;</span>
                <div class="flex items-center gap-1.5">
                    <i data-lucide="database" class="w-3.5 h-3.5 text-theme-primary"></i>
                    <span>Secure Database Auth</span>
                </div>
            </div>

        </div>

        <!-- Vendor Link -->
        <div class="mt-6 text-center text-xs text-zinc-400">
            <span>Are you a photographer partner?</span>
            <a href="{{ route('vendor.register.show') }}" class="text-theme-primary hover:underline ml-1 font-semibold">
                Vendor Hub &rarr;
            </a>
        </div>
    </div>
</div>
@endsection
