@extends('layouts.app')

@section('content')
<div class="relative overflow-hidden py-16 px-4 sm:px-6 lg:px-8">
    <!-- Ambient Background Glows -->
    <div class="hero-glow top-20 left-10 opacity-60"></div>
    <div class="hero-glow-gold bottom-20 right-10 opacity-40"></div>

    <div class="max-w-5xl mx-auto relative z-10 space-y-12">
        <!-- Header -->
        <div class="text-center max-w-3xl mx-auto space-y-4 reveal">
            <span class="text-xs font-bold uppercase tracking-widest text-theme-primary">
                {{ $page->subtitle ?? ($siteSettings['site_name'] ?? 'Middukhera Production') }}
            </span>
            <h1 class="font-serif text-3xl sm:text-5xl font-bold text-white leading-tight">
                {{ $page->title }}
            </h1>
            <div class="w-20 h-0.5 bg-gradient-to-r from-transparent via-[var(--theme-primary)] to-transparent mx-auto mt-4 mb-6"></div>
        </div>

        @if(!empty($page->banner_image))
            <div class="rounded-3xl overflow-hidden border border-white/10 shadow-2xl max-h-[450px] reveal">
                <img src="{{ $page->banner_image }}" alt="{{ $page->title }}" class="w-full h-full object-cover">
            </div>
        @endif

        <!-- Body Content -->
        <div class="site-card rounded-3xl border border-white/10 p-8 sm:p-12 space-y-6 text-sm text-zinc-300 leading-relaxed shadow-2xl reveal">
            {!! $page->content !!}
        </div>

        <!-- Back to Home CTA -->
        <div class="text-center pt-6">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-full text-xs font-bold uppercase tracking-wider btn-gold-dynamic shadow-xl hover:scale-105 transition-all">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Return to Home</span>
            </a>
        </div>
    </div>
</div>
@endsection
