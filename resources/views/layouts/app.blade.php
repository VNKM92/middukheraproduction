<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Dynamic SEO Meta Tags -->
        <title>{{ $meta_title ?? $siteSettings['meta_title'] ?? 'Middukhera Production — Luxury Studio & Media Production' }}</title>
        <meta name="description" content="{{ $meta_description ?? $siteSettings['meta_description'] ?? 'Middukhera Production is a premier photography and production studio offering wedding, fashion, portrait and event photography services.' }}">
        <meta name="keywords" content="{{ $meta_keywords ?? $siteSettings['meta_keywords'] ?? 'Middukhera Production, photoshoot, studio, photography, wedding photography, fashion photography, Razorpay payment' }}">
        <link rel="canonical" href="{{ $meta_canonical ?? url()->current() }}" />

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="website" />
        <meta property="og:site_name" content="{{ $siteSettings['site_name'] ?? 'Middukhera Production' }}" />
        <meta property="og:title" content="{{ $meta_title ?? $siteSettings['meta_title'] ?? 'Middukhera Production' }}" />
        <meta property="og:description" content="{{ $meta_description ?? $siteSettings['meta_description'] ?? 'Middukhera Production is a premier photoshoot studio offering wedding, fashion, portrait and event photography services.' }}" />
        <meta property="og:image" content="{{ $meta_image ?? $siteSettings['hero_bg_image'] ?? asset('favicon.ico') }}" />
        <meta property="og:url" content="{{ $meta_canonical ?? url()->current() }}" />

        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="{{ $meta_title ?? $siteSettings['meta_title'] ?? 'Middukhera Production' }}" />
        <meta name="twitter:description" content="{{ $meta_description ?? $siteSettings['meta_description'] ?? 'Middukhera Production is a premier photoshoot studio offering wedding, fashion, portrait and event photography services.' }}" />
        <meta name="twitter:image" content="{{ $meta_image ?? $siteSettings['hero_bg_image'] ?? asset('favicon.ico') }}" />

        <!-- Structured Data (JSON-LD) for LocalBusiness & PhotographyStudio -->
        <script type="application/ld+json">
        {!! json_encode([
          '@context' => 'https://schema.org',
          '@type' => 'PhotographyStudio',
          'name' => $siteSettings['site_name'] ?? 'Middukhera Production',
          'url' => url('/'),
          'image' => $siteSettings['hero_bg_image'] ?? 'https://images.unsplash.com/photo-1542038784456-1ea8e935640e',
          'description' => $siteSettings['meta_description'] ?? 'Luxury Photoshoot & Production Studio',
          'telephone' => $siteSettings['contact_phone'] ?? '+91 98765 43210',
          'email' => $siteSettings['contact_email'] ?? 'contact@middukheraproduction.in',
          'priceRange' => ($siteSettings['currency_symbol'] ?? '₹') . '15,000 - ' . ($siteSettings['currency_symbol'] ?? '₹') . '5,00,000',
          'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => $siteSettings['contact_address'] ?? 'Middukhera Production Studio, India',
          ],
          'openingHours' => 'Mo-Su 09:00-21:00'
        ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
        </script>

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">

        <!-- Lucide Icons CDN -->
        <script src="https://unpkg.com/lucide@latest"></script>

        <!-- Alpine.js -->
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <!-- Dynamic Root Theme CSS Variables (Controlled via Super Admin Settings) -->
        <style>
            :root {
                --theme-primary: {{ $siteSettings['primary_color'] ?? '#E5C158' }};
                --theme-primary-hover: {{ $siteSettings['primary_hover'] ?? '#F3D88B' }};
                --theme-secondary: {{ $siteSettings['secondary_color'] ?? '#B8922E' }};
                --theme-accent: {{ $siteSettings['accent_color'] ?? '#8B5CF6' }};
                --theme-bg: {{ $siteSettings['bg_color'] ?? '#07060a' }};
                --theme-card: {{ $siteSettings['card_bg_color'] ?? '#12101b' }};
                --theme-text: {{ $siteSettings['text_color'] ?? '#F3F4F6' }};
                --theme-text-muted: {{ $siteSettings['text_muted'] ?? '#9CA3AF' }};
                --theme-border: {{ $siteSettings['border_color'] ?? 'rgba(255, 255, 255, 0.08)' }};
            }

            body {
                background-color: var(--theme-bg) !important;
                color: var(--theme-text) !important;
            }

            .site-bg {
                background-color: var(--theme-bg) !important;
            }

            .site-card {
                background-color: var(--theme-card) !important;
                border-color: var(--theme-border) !important;
            }

            .text-theme-primary {
                color: var(--theme-primary) !important;
            }

            .bg-theme-primary {
                background-color: var(--theme-primary) !important;
            }

            .border-theme-primary {
                border-color: var(--theme-primary) !important;
            }

            .text-theme-accent {
                color: var(--theme-accent) !important;
            }

            .bg-theme-accent {
                background-color: var(--theme-accent) !important;
            }

            .border-theme-accent {
                border-color: var(--theme-accent) !important;
            }

            .theme-gradient-text {
                background: linear-gradient(135deg, var(--theme-primary-hover) 0%, var(--theme-primary) 50%, var(--theme-secondary) 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <link rel="manifest" href="{{ asset('manifest.json') }}">
        <link rel="manifest" href="{{ asset('build/manifest.json') }}">

         <link rel="preload" as="style" href="{{url('public/build/assets/app-qCqL4GTJ.css')}}" />

         <link rel="modulepreload" as="script" href="{{url('public/build/assets/app-D-c50wYQ.js')}}" />
         
         <link rel="stylesheet" href="{{url('public/build/assets/app-qCqL4GTJ.css')}}" />
         
         <script type="module" src="{{url('public/build/assets/app-D-c50wYQ.js')}}"></script>


    </head>
    <body class="font-sans antialiased dynamic-theme selection:bg-[var(--theme-primary)] selection:text-black">
        @if(request()->routeIs('admin.*'))
            <!-- Full-Screen Dedicated Executive Admin Layout -->
            <div class="min-h-screen site-bg">
                <!-- Global Admin Toast Notifications -->
                @if (session('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)" class="fixed top-4 right-4 z-50 max-w-md transition duration-300">
                        <div class="p-4 rounded-xl site-card border border-emerald-500/30 text-emerald-300 flex items-center justify-between gap-3 shadow-2xl backdrop-blur-xl bg-black/80">
                            <div class="flex items-center gap-2.5">
                                <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-400 shrink-0"></i>
                                <span class="text-xs font-semibold">{{ session('success') }}</span>
                            </div>
                            <button @click="show = false" class="text-zinc-400 hover:text-white"><i data-lucide="x" class="w-4 h-4"></i></button>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 8000)" class="fixed top-4 right-4 z-50 max-w-md transition duration-300">
                        <div class="p-4 rounded-xl site-card border border-rose-500/30 text-rose-300 flex items-center justify-between gap-3 shadow-2xl backdrop-blur-xl bg-black/80">
                            <div class="flex items-center gap-2.5">
                                <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-400 shrink-0"></i>
                                <span class="text-xs font-semibold">{{ session('error') }}</span>
                            </div>
                            <button @click="show = false" class="text-zinc-400 hover:text-white"><i data-lucide="x" class="w-4 h-4"></i></button>
                        </div>
                    </div>
                @endif

                @if(isset($slot))
                    {{ $slot }}
                @else
                    @yield('content')
                @endif
            </div>
        @else
            <!-- Public Website / Client Portal Layout -->
            <div class="min-h-screen site-bg flex flex-col justify-between">
                <div>
                    @include('layouts.navigation')

                    <!-- Flash Messages -->
                    @if (session('success'))
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                            <div class="p-4 rounded-xl site-card border border-emerald-500/30 text-emerald-300 flex items-center gap-3 shadow-lg shadow-emerald-900/10">
                                <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-400 shrink-0"></i>
                                <span class="text-sm font-medium">{{ session('success') }}</span>
                            </div>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                            <div class="p-4 rounded-xl site-card border border-rose-500/30 text-rose-300 flex items-center gap-3 shadow-lg shadow-rose-900/10">
                                <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-400 shrink-0"></i>
                                <span class="text-sm font-medium">{{ session('error') }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Page Heading -->
                    @isset($header)
                        <header class="site-card border-b border-white/5 shadow-sm backdrop-blur-md sticky top-16 z-30">
                            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                                {{ $header }}
                            </div>
                        </header>
                    @endisset

                    <!-- Page Content -->
                    <main>
                        @if(isset($slot))
                            {{ $slot }}
                        @else
                            @yield('content')
                        @endif
                    </main>
                </div>

                <!-- Global Footer -->
                @include('layouts.footer')
            </div>
        @endif

        <!-- Scroll Reveal & Lucide Initializer Script -->
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }

                const revealElements = document.querySelectorAll('.reveal');
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('active');
                        }
                    });
                }, {
                    threshold: 0.05,
                    rootMargin: '0px 0px -40px 0px'
                });

                revealElements.forEach(el => observer.observe(el));
            });
        </script>
        @stack('scripts')
    </body>
</html>
