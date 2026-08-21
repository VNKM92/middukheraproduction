@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-16 px-4 sm:px-6 lg:px-8 space-y-10">
    <div class="text-center space-y-3">
        <span class="text-xs font-bold uppercase tracking-widest text-theme-primary">Legal & Compliance</span>
        <h1 class="text-3xl sm:text-4xl font-serif font-bold text-white">Privacy Policy</h1>
        <div class="w-16 h-0.5 bg-theme-primary mx-auto mt-3 mb-2"></div>
        <p class="text-xs text-zinc-400">Effective Date: {{ date('F Y') }} &bull; {{ $siteSettings['site_name'] ?? 'Middukhera Production' }}</p>
    </div>

    <div class="site-card rounded-3xl border border-white/10 p-8 sm:p-12 space-y-8 text-xs text-zinc-300 leading-relaxed shadow-2xl">
        <section class="space-y-3">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="text-theme-primary font-mono">01.</span>
                <span>Overview & Commitment</span>
            </h2>
            <p>
                At <strong>{{ $siteSettings['site_name'] ?? 'Middukhera Production' }}</strong> (accessible at <a href="{{ url('/') }}" class="text-theme-primary hover:underline">{{ url('/') }}</a>), we are committed to protecting and respecting your personal privacy. This Privacy Policy outlines the types of information we collect, how it is used, stored, and protected when you visit our website or book our photography and media production services.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="text-theme-primary font-mono">02.</span>
                <span>Information We Collect</span>
            </h2>
            <p>When you interact with our website or reserve a studio session, we may collect the following personal information:</p>
            <ul class="list-disc list-inside space-y-1.5 pl-2 text-zinc-400">
                <li><strong class="text-white">Contact Information:</strong> Full name, email address, phone number, and physical billing/shipping address.</li>
                <li><strong class="text-white">Booking Details:</strong> Desired shoot dates, chosen package, location preferences, and styling notes.</li>
                <li><strong class="text-white">Transaction Information:</strong> Order IDs, transaction reference numbers, and payment status generated during checkout.</li>
                <li><strong class="text-white">Technical Data:</strong> IP address, browser type, device information, and standard web log statistics collected automatically to enhance user experience.</li>
            </ul>
        </section>

        <section class="space-y-3">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="text-theme-primary font-mono">03.</span>
                <span>Payment Processing & Security (Razorpay)</span>
            </h2>
            <p>
                We do not store, process, or retain sensitive card numbers, CVVs, or banking PINs on our servers. All financial transactions are securely processed through our certified payment gateway partner, <strong>Razorpay Software Private Limited</strong>.
            </p>
            <p>
                Razorpay adheres to the highest level of Payment Card Industry Data Security Standards (<strong class="text-white">PCI-DSS Level 1</strong> compliant) and uses 256-bit SSL encryption to guarantee that all payment information transmitted is completely protected and confidential.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="text-theme-primary font-mono">04.</span>
                <span>How We Use Your Information</span>
            </h2>
            <p>We use your collected information strictly for legitimate business purposes:</p>
            <ul class="list-disc list-inside space-y-1.5 pl-2 text-zinc-400">
                <li>To confirm, manage, and schedule your photoshoot bookings.</li>
                <li>To process payments, issue invoices, and provide booking confirmations via SMS and email.</li>
                <li>To deliver digital proofing sheets, online galleries, and shipped physical photo albums.</li>
                <li>To respond to your inquiries submitted via our contact concierge.</li>
                <li>To comply with applicable legal obligations and dispute resolution.</li>
            </ul>
        </section>

        <section class="space-y-3">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="text-theme-primary font-mono">05.</span>
                <span>Client Media & Image Privacy</span>
            </h2>
            <p>
                We respect your personal and private moments. Client photo galleries and raw files are stored in secured, password-protected private directories. Images from private events (weddings, family portraits, personal branding) will never be used in our public portfolio or promotional media without your prior written authorization.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="text-theme-primary font-mono">06.</span>
                <span>Cookies & Tracking Technologies</span>
            </h2>
            <p>
                Our website uses standard cookies and session identifiers to facilitate smooth navigation, maintain authenticated client sessions, and analyze site performance. You can choose to disable cookies through your browser settings, though some interactive features may function with limitations.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="text-theme-primary font-mono">07.</span>
                <span>Data Retention & Your Rights</span>
            </h2>
            <p>
                We retain client booking data only for as long as necessary to fulfill the requested services and satisfy accounting, tax, or legal requirements. You have the right to request access to, correction of, or deletion of your personal data by reaching out to our Grievance Desk.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="text-theme-primary font-mono">08.</span>
                <span>Contact Our Privacy & Grievance Officer</span>
            </h2>
            <p>If you have any questions or concerns regarding this Privacy Policy, please reach out to:</p>
            <div class="p-4 rounded-2xl bg-white/5 border border-white/5 space-y-1.5 text-zinc-300">
                <p><strong class="text-white">{{ $siteSettings['site_name'] ?? 'Middukhera Production' }}</strong></p>
                <p>Email: <a href="mailto:{{ $siteSettings['contact_email'] ?? 'contact@middukheraproduction.in' }}" class="text-theme-primary hover:underline">{{ $siteSettings['contact_email'] ?? 'contact@middukheraproduction.in' }}</a></p>
                <p>Phone: <a href="tel:{{ $siteSettings['contact_phone'] ?? '+919876543210' }}" class="text-theme-primary hover:underline">{{ $siteSettings['contact_phone'] ?? '+91 98765 43210' }}</a></p>
                <p>Address: {{ $siteSettings['contact_address'] ?? 'Middukhera Production Studio, India' }}</p>
                <p>Operating Hours: {{ $siteSettings['operating_hours'] ?? 'Mon - Sun: 09:00 AM - 09:00 PM IST' }}</p>
            </div>
        </section>
    </div>
</div>
@endsection
