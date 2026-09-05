@extends('layouts.app')

@section('content')
<div class="relative min-h-[calc(100vh-80px)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 overflow-hidden">
    <!-- Ambient Backdrop Effects -->
    <div class="absolute top-1/3 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[550px] h-[550px] bg-[var(--theme-primary)]/10 rounded-full blur-[140px] pointer-events-none"></div>
    <div class="absolute -bottom-20 -right-20 w-80 h-80 bg-purple-500/10 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="w-full max-w-xl relative z-10">
        <!-- Brand Header / Badge -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full border border-[var(--theme-primary)]/30 bg-[var(--theme-primary)]/10 text-theme-primary text-xs font-semibold tracking-wider uppercase mb-3 shadow-inner">
                <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                <span>Client Registration</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-serif font-bold text-white tracking-tight">
                Create Your Studio Account
            </h1>
            <p class="text-sm text-zinc-400 mt-2 max-w-md mx-auto">
                Join {{ $siteSettings['site_name'] ?? 'Middukhera Production' }} to reserve exclusive studio sessions, access your private digital proofs, and download high-resolution master galleries.
            </p>
        </div>

        <!-- Registration Card -->
        <div class="site-card rounded-3xl border border-white/10 p-7 sm:p-10 shadow-2xl backdrop-blur-xl relative">
            
            <!-- Global / Validation Errors -->
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs space-y-1">
                    <div class="font-bold flex items-center gap-2 text-rose-200">
                        <i data-lucide="alert-circle" class="w-4 h-4 text-rose-400"></i>
                        <span>Please correct the errors below:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 pl-1 pt-1 text-zinc-300">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-5" x-data="{ showPass: false, showConfirm: false }">
                @csrf

                <!-- Full Name -->
                <div>
                    <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-zinc-300 mb-2">
                        Full Name <span class="text-[var(--theme-primary)]">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                            <i data-lucide="user" class="w-4 h-4"></i>
                        </div>
                        <input id="name"
                               type="text"
                               name="name"
                               value="{{ old('name') }}"
                               required
                               autofocus
                               autocomplete="name"
                               placeholder="e.g. Vinay Kumar"
                               class="w-full pl-10 pr-4 py-3 text-sm bg-black/40 border {{ $errors->has('name') ? 'border-rose-500/80 focus:ring-rose-500' : 'border-white/10 focus:border-[var(--theme-primary)] focus:ring-[var(--theme-primary)]' }} rounded-xl text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-opacity-40 transition-all duration-200" />
                    </div>
                    @error('name')
                        <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-zinc-300 mb-2">
                        Email Address <span class="text-[var(--theme-primary)]">*</span>
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
                               autocomplete="username"
                               placeholder="you@example.com"
                               class="w-full pl-10 pr-4 py-3 text-sm bg-black/40 border {{ $errors->has('email') ? 'border-rose-500/80 focus:ring-rose-500' : 'border-white/10 focus:border-[var(--theme-primary)] focus:ring-[var(--theme-primary)]' }} rounded-xl text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-opacity-40 transition-all duration-200" />
                    </div>
                    @error('email')
                        <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Fields Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-zinc-300 mb-2">
                            Password <span class="text-[var(--theme-primary)]">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                                <i data-lucide="lock" class="w-4 h-4"></i>
                            </div>
                            <input id="password"
                                   :type="showPass ? 'text' : 'password'"
                                   name="password"
                                   required
                                   autocomplete="new-password"
                                   placeholder="At least 8 chars"
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

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-zinc-300 mb-2">
                            Confirm Password <span class="text-[var(--theme-primary)]">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                                <i data-lucide="shield-check" class="w-4 h-4"></i>
                            </div>
                            <input id="password_confirmation"
                                   :type="showConfirm ? 'text' : 'password'"
                                   name="password_confirmation"
                                   required
                                   autocomplete="new-password"
                                   placeholder="Re-enter password"
                                   class="w-full pl-10 pr-10 py-3 text-sm bg-black/40 border {{ $errors->has('password_confirmation') ? 'border-rose-500/80 focus:ring-rose-500' : 'border-white/10 focus:border-[var(--theme-primary)] focus:ring-[var(--theme-primary)]' }} rounded-xl text-white placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-opacity-40 transition-all duration-200" />
                            <button type="button"
                                    @click="showConfirm = !showConfirm"
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-zinc-400 hover:text-white transition">
                                <i data-lucide="eye" x-show="!showConfirm" class="w-4 h-4"></i>
                                <i data-lucide="eye-off" x-show="showConfirm" class="w-4 h-4" style="display:none;"></i>
                            </button>
                        </div>
                        @error('password_confirmation')
                            <p class="text-rose-400 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Terms Notice -->
                <div class="pt-1">
                    <p class="text-xs text-zinc-400 leading-relaxed">
                        By registering, you agree to our 
                        <a href="{{ route('terms') }}" class="text-theme-primary hover:underline" target="_blank">Terms & Conditions</a> and 
                        <a href="{{ route('privacy') }}" class="text-theme-primary hover:underline" target="_blank">Privacy Policy</a>.
                    </p>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        class="w-full py-3.5 px-6 rounded-xl font-bold text-xs sm:text-sm uppercase tracking-wider btn-gold-dynamic shadow-xl shadow-[var(--theme-primary)]/20 flex items-center justify-center gap-2 mt-2">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                    <span>Create Client Account</span>
                </button>
            </form>

            <!-- Divider -->
            <div class="relative my-6 text-center">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-white/10"></div>
                </div>
                <div class="relative inline-block px-4 bg-[#12101b] text-xs uppercase tracking-widest text-zinc-500 font-semibold">
                    Already Registered?
                </div>
            </div>

            <!-- Sign In Redirect Link -->
            <div class="text-center">
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 w-full py-3 px-4 rounded-xl text-sm font-semibold text-zinc-200 border border-white/10 hover:border-[var(--theme-primary)]/50 hover:bg-white/5 transition-all duration-200">
                    <i data-lucide="log-in" class="w-4 h-4 text-theme-primary"></i>
                    <span>Sign In to Existing Account</span>
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
                    <span>Instant Database Sync</span>
                </div>
                <span class="text-zinc-600">&bull;</span>
                <div class="flex items-center gap-1.5">
                    <i data-lucide="lock" class="w-3.5 h-3.5 text-indigo-400"></i>
                    <span>Bcrypt Password Security</span>
                </div>
            </div>

        </div>

        <!-- Vendor Portal Link -->
        <div class="mt-6 text-center text-xs text-zinc-400">
            <span>Are you a photographer or studio partner?</span>
            <a href="{{ route('vendor.register.show') }}" class="text-theme-primary hover:underline ml-1 font-semibold">
                Register as Vendor Partner &rarr;
            </a>
        </div>
    </div>
</div>
@endsection
