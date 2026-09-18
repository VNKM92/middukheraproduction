@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-16 px-4 sm:px-6 lg:px-8 space-y-10">
    <div class="text-center space-y-3 reveal">
        <span class="text-xs font-bold uppercase tracking-widest text-theme-primary">
            {{ $page->subtitle ?? 'Legal & Fulfillment' }}
        </span>
        <h1 class="text-3xl sm:text-4xl font-serif font-bold text-white">
            {{ $page->title ?? 'Shipping & Delivery Policy' }}
        </h1>
        <div class="w-16 h-0.5 bg-theme-primary mx-auto mt-3 mb-2"></div>
        <p class="text-xs text-zinc-400">Effective Date: {{ $page ? $page->updated_at->format('F Y') : date('F Y') }} &bull; {{ $siteSettings['site_name'] ?? 'Middukhera Production' }}</p>
    </div>

    <div class="site-card rounded-3xl border border-white/10 p-8 sm:p-12 space-y-8 text-xs text-zinc-300 leading-relaxed shadow-2xl reveal">
        @if($page && !empty($page->content))
            {!! $page->content !!}
        @else
            <section class="space-y-3">
                <h2 class="text-base font-bold text-white flex items-center gap-2">
                    <span class="text-theme-primary font-mono">01.</span>
                    <span>Overview</span>
                </h2>
                <p>
                    Our photography and production services include digital deliverables and premium physical deliverables.
                </p>
            </section>
        @endif
    </div>
</div>
@endsection
