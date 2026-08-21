@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-16 px-4 sm:px-6 lg:px-8 space-y-10">
    <div class="text-center space-y-3">
        <span class="text-xs font-bold uppercase tracking-widest text-theme-primary">Legal & Compliance</span>
        <h1 class="text-3xl sm:text-4xl font-serif font-bold text-white">Terms and Conditions</h1>
        <div class="w-16 h-0.5 bg-theme-primary mx-auto mt-3 mb-2"></div>
        <p class="text-xs text-zinc-400">Last updated: {{ date('F Y') }} &bull; {{ $siteSettings['site_name'] ?? 'Middukhera Production' }}</p>
    </div>

    <div class="site-card rounded-3xl border border-white/10 p-8 sm:p-12 space-y-8 text-xs text-zinc-300 leading-relaxed shadow-2xl">
        <section class="space-y-3">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="text-theme-primary font-mono">01.</span>
                <span>Agreement to Terms</span>
            </h2>
            <p>
                These Terms and Conditions constitute a legally binding agreement between you (whether personally or on behalf of an entity, "Client") and <strong>{{ $siteSettings['site_name'] ?? 'Middukhera Production' }}</strong> ("we", "us", or "our"), concerning your access to and use of our website (<a href="{{ url('/') }}" class="text-theme-primary hover:underline">{{ url('/') }}</a>) and our photography, cinematography, and production services.
            </p>
            <p>
                By accessing this website or reserving a photoshoot package, you acknowledge that you have read, understood, and agree to be bound by all of these Terms and Conditions.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="text-theme-primary font-mono">02.</span>
                <span>Booking, Deposits & Razorpay Payments</span>
            </h2>
            <p>
                All photoshoot reservations require payment of a deposit or the full package amount at the time of booking. Payments on our website are securely processed via <strong>Razorpay</strong>.
            </p>
            <ul class="list-disc list-inside space-y-1.5 pl-2 text-zinc-400">
                <li>Prices are displayed and charged in Indian National Rupees (<strong class="text-white">INR / ₹</strong>).</li>
                <li>Your booking is confirmed immediately upon successful capture of the transaction through Razorpay.</li>
                <li>An automated electronic receipt and booking reference ID will be issued upon transaction completion.</li>
            </ul>
        </section>

        <section class="space-y-3">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="text-theme-primary font-mono">03.</span>
                <span>Cancellation, Rescheduling & Refunds</span>
            </h2>
            <p>
                Cancellations and rescheduling are governed by our official <a href="{{ route('refund-policy') }}" class="text-theme-primary hover:underline">Cancellation & Refund Policy</a>.
            </p>
            <ul class="list-disc list-inside space-y-1.5 pl-2 text-zinc-400">
                <li>Rescheduling requests made at least 5 days in advance are accommodated at zero extra charge subject to availability.</li>
                <li>All eligible refunds approved by {{ $siteSettings['site_name'] ?? 'Middukhera Production' }} are credited back to the original payment source within <strong>5 to 7 working days</strong>.</li>
            </ul>
        </section>

        <section class="space-y-3">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="text-theme-primary font-mono">04.</span>
                <span>Deliverables & Fulfillment Timelines</span>
            </h2>
            <p>
                Deliverables are fulfilled according to our <a href="{{ route('shipping-policy') }}" class="text-theme-primary hover:underline">Shipping & Delivery Policy</a>:
            </p>
            <ul class="list-disc list-inside space-y-1.5 pl-2 text-zinc-400">
                <li>Unedited preview contact sheets are uploaded within 48 to 72 hours of the session.</li>
                <li>Master color-graded and retouched high-resolution digital files are delivered within 7 to 14 business days via digital download.</li>
                <li>Physical albums and fine art prints are dispatched via insured couriers within 15 to 21 working days across India.</li>
            </ul>
        </section>

        <section class="space-y-3">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="text-theme-primary font-mono">05.</span>
                <span>Intellectual Property & Usage Rights</span>
            </h2>
            <p>
                {{ $siteSettings['site_name'] ?? 'Middukhera Production' }} retains copyright in all photographs and media created during sessions. Clients receive a non-exclusive, perpetual, personal license to print, share, and display the delivered media. Commercial usage rights are granted according to the specific commercial tier purchased.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="text-theme-primary font-mono">06.</span>
                <span>Limitation of Liability & Force Majeure</span>
            </h2>
            <p>
                Neither party shall be held liable for failure or delay in performance caused by circumstances beyond reasonable control, including acts of God, extreme weather, government regulations, or technological infrastructure outages. In such instances, both parties will cooperate in good faith to reschedule.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="text-theme-primary font-mono">07.</span>
                <span>Governing Law & Jurisdiction</span>
            </h2>
            <p>
                These Terms and Conditions shall be governed by and construed in accordance with the laws of <strong>India</strong>. Any legal dispute or claim arising out of these terms shall be subject to the exclusive jurisdiction of the competent courts in India.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="text-theme-primary font-mono">08.</span>
                <span>Contact & Grievance Information</span>
            </h2>
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
