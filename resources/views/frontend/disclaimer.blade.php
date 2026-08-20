@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-16 px-4 sm:px-6 lg:px-8 space-y-10">
    <div class="text-center space-y-3">
        <span class="text-xs font-bold uppercase tracking-widest text-theme-primary">Legal Notice</span>
        <h1 class="text-3xl sm:text-4xl font-serif font-bold text-white">Disclaimer & Privacy Policy</h1>
        <div class="w-16 h-0.5 bg-theme-primary mx-auto mt-3 mb-2"></div>
        <p class="text-xs text-zinc-400">Published by {{ $siteSettings['site_name'] ?? 'Middukhera Studio & Productions' }}</p>
    </div>

    <div class="site-card rounded-3xl border border-white/10 p-8 sm:p-12 space-y-8 text-xs text-zinc-300 leading-relaxed shadow-2xl">
        <section class="space-y-3">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="text-theme-primary font-mono">01.</span>
                <span>General Information Disclaimer</span>
            </h2>
            <p>
                All imagery, package descriptions, and pricing specifications published on this platform are provided for informational and reservation purposes by {{ $siteSettings['site_name'] ?? 'Middukhera Studio' }}. We reserve the right to revise tier deliverables, add-ons, and pricing structures at our sole discretion prior to booking confirmation.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="text-theme-primary font-mono">02.</span>
                <span>Payment Processing & Security</span>
            </h2>
            <p>
                Financial transactions and credit/debit/UPI payments are securely processed via Razorpay Software Private Limited. We do not store raw card numbers, CVVs, or sensitive banking passwords on our local servers.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="text-theme-primary font-mono">03.</span>
                <span>Data Privacy & Client Confidentiality</span>
            </h2>
            <p>
                We value your privacy. Client contact details, booking information, and delivered gallery plates are treated with strict confidentiality. High-profile private wedding archives will never be displayed publicly on social media or portfolio without explicit written consent.
            </p>
        </section>
    </div>
</div>
@endsection
