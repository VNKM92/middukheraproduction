@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-16 px-4 sm:px-6 lg:px-8 space-y-10">
    <div class="text-center space-y-3">
        <span class="text-xs font-bold uppercase tracking-widest text-theme-primary">Legal Notice</span>
        <h1 class="text-3xl sm:text-4xl font-serif font-bold text-white">Disclaimer</h1>
        <div class="w-16 h-0.5 bg-theme-primary mx-auto mt-3 mb-2"></div>
        <p class="text-xs text-zinc-400">Published by {{ $siteSettings['site_name'] ?? 'Middukhera Production' }}</p>
    </div>

    <div class="site-card rounded-3xl border border-white/10 p-8 sm:p-12 space-y-8 text-xs text-zinc-300 leading-relaxed shadow-2xl">
        <section class="space-y-3">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="text-theme-primary font-mono">01.</span>
                <span>General Information Disclaimer</span>
            </h2>
            <p>
                All imagery, package descriptions, and pricing specifications published on this platform (<a href="{{ url('/') }}" class="text-theme-primary hover:underline">{{ url('/') }}</a>) are provided for informational and reservation purposes by <strong>{{ $siteSettings['site_name'] ?? 'Middukhera Production' }}</strong>. We reserve the right to revise tier deliverables, add-ons, and pricing structures at our sole discretion prior to booking confirmation.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="text-theme-primary font-mono">02.</span>
                <span>Payment Processing & Security</span>
            </h2>
            <p>
                Financial transactions and credit/debit/UPI payments are securely processed via Razorpay Software Private Limited. We do not store raw card numbers, CVVs, or sensitive banking passwords on our local servers. For full details on payment processing security, please review our <a href="{{ route('privacy') }}" class="text-theme-primary hover:underline">Privacy Policy</a>.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="text-theme-primary font-mono">03.</span>
                <span>Third-Party Links & External Content</span>
            </h2>
            <p>
                Our website may contain links to external third-party websites or services that are not owned or controlled by {{ $siteSettings['site_name'] ?? 'Middukhera Production' }}. We do not assume responsibility for the content, privacy practices, or accuracy of any third-party websites.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="text-theme-primary font-mono">04.</span>
                <span>Policy References</span>
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                <a href="{{ route('terms') }}" class="p-3 rounded-xl bg-white/5 border border-white/5 hover:border-theme-primary transition flex items-center justify-between">
                    <span class="font-medium text-white">Terms and Conditions</span>
                    <i data-lucide="arrow-right" class="w-4 h-4 text-theme-primary"></i>
                </a>
                <a href="{{ route('privacy') }}" class="p-3 rounded-xl bg-white/5 border border-white/5 hover:border-theme-primary transition flex items-center justify-between">
                    <span class="font-medium text-white">Privacy Policy</span>
                    <i data-lucide="arrow-right" class="w-4 h-4 text-theme-primary"></i>
                </a>
                <a href="{{ route('refund-policy') }}" class="p-3 rounded-xl bg-white/5 border border-white/5 hover:border-theme-primary transition flex items-center justify-between">
                    <span class="font-medium text-white">Cancellation & Refund Policy</span>
                    <i data-lucide="arrow-right" class="w-4 h-4 text-theme-primary"></i>
                </a>
                <a href="{{ route('shipping-policy') }}" class="p-3 rounded-xl bg-white/5 border border-white/5 hover:border-theme-primary transition flex items-center justify-between">
                    <span class="font-medium text-white">Shipping & Delivery Policy</span>
                    <i data-lucide="arrow-right" class="w-4 h-4 text-theme-primary"></i>
                </a>
            </div>
        </section>
    </div>
</div>
@endsection
