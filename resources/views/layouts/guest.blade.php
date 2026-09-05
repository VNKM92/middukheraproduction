<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Middukhera Production') }}</title>

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">

        <!-- Lucide Icons -->
        <script src="https://unpkg.com/lucide@latest"></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <link rel="manifest" href="{{url('/public/build/manifest.json')}}" />

        <style>
            :root {
                --theme-primary: {{ $siteSettings['primary_color'] ?? '#E5C158' }};
                --theme-primary-hover: {{ $siteSettings['primary_hover'] ?? '#F3D88B' }};
                --theme-bg: {{ $siteSettings['bg_color'] ?? '#07060a' }};
                --theme-card: {{ $siteSettings['card_bg_color'] ?? '#12101b' }};
                --theme-border: {{ $siteSettings['border_color'] ?? 'rgba(255, 255, 255, 0.08)' }};
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-[#07060a] text-zinc-100 selection:bg-[var(--theme-primary)] selection:text-black min-h-screen">
        <div class="min-h-screen flex flex-col sm:justify-center items-center py-12 px-4 sm:px-6 relative overflow-hidden">
            <!-- Ambient Glow -->
            <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-[var(--theme-primary)]/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="mb-6 z-10 text-center">
                <a href="{{ route('home') }}" class="inline-flex flex-col items-center gap-1 group">
                    <span class="font-serif text-2xl sm:text-3xl font-bold tracking-widest text-white group-hover:text-[var(--theme-primary)] transition-colors">
                        {{ $siteSettings['logo_text'] ?? 'Middukhera' }}
                    </span>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded tracking-widest bg-[var(--theme-primary)] text-black">
                        {{ $siteSettings['logo_sub'] ?? 'PRODUCTION' }}
                    </span>
                </a>
            </div>

            <div class="w-full sm:max-w-md px-6 py-8 site-card bg-[#12101b]/90 backdrop-blur-xl border border-white/10 rounded-3xl shadow-2xl z-10">
                {{ $slot }}
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            });
        </script>
    </body>
</html>
