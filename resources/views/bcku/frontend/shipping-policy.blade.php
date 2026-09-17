@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-16 px-4 sm:px-6 lg:px-8 space-y-10">
    <div class="text-center space-y-3">
        <span class="text-xs font-bold uppercase tracking-widest text-theme-primary">Legal & Compliance</span>
        <h1 class="text-3xl sm:text-4xl font-serif font-bold text-white">Shipping & Delivery Policy</h1>
        <div class="w-16 h-0.5 bg-theme-primary mx-auto mt-3 mb-2"></div>
        <p class="text-xs text-zinc-400">Effective Date: {{ date('F Y') }} &bull; {{ $siteSettings['site_name'] ?? 'Middukhera Production' }}</p>
    </div>

    <div class="site-card rounded-3xl border border-white/10 p-8 sm:p-12 space-y-8 text-xs text-zinc-300 leading-relaxed shadow-2xl">
        <section class="space-y-3">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="text-theme-primary font-mono">01.</span>
                <span>Overview of Services & Deliverables</span>
            </h2>
            <p>
                <strong>{{ $siteSettings['site_name'] ?? 'Middukhera Production' }}</strong> provides professional photography, videography, and multimedia production services. Our deliverables consist of both <strong>Digital Assets</strong> (high-resolution color-graded photos, cinematic videos, contact sheets) and <strong>Physical Artifacts</strong> (handcrafted photo albums, framed museum-grade prints, acrylic mounts).
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="text-theme-primary font-mono">02.</span>
                <span>Digital Deliverables & Timelines</span>
            </h2>
            <p>Digital media is delivered electronically via secure cloud galleries and the Client Dashboard:</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div class="p-4 rounded-2xl bg-white/5 border border-white/5 space-y-1.5">
                    <div class="text-theme-primary font-bold text-sm">Preview Contact Sheets</div>
                    <div class="text-white font-semibold">Within 48 to 72 Hours</div>
                    <p class="text-zinc-400 text-[11px]">Unretouched digital contact sheet for client selection made accessible directly in your account.</p>
                </div>

                <div class="p-4 rounded-2xl bg-white/5 border border-white/5 space-y-1.5">
                    <div class="text-theme-primary font-bold text-sm">Master Retouched Plates</div>
                    <div class="text-white font-semibold">7 to 14 Working Days</div>
                    <p class="text-zinc-400 text-[11px]">Fully graded, high-resolution JPEG and TIFF deliverables downloadable with commercial licensing.</p>
                </div>

                <div class="p-4 rounded-2xl bg-white/5 border border-white/5 space-y-1.5">
                    <div class="text-theme-primary font-bold text-sm">Cinematic Highlight Videos</div>
                    <div class="text-white font-semibold">14 to 21 Working Days</div>
                    <p class="text-zinc-400 text-[11px]">4K edited teasers and cinematic documentary films provided via private streaming links and direct download.</p>
                </div>

                <div class="p-4 rounded-2xl bg-white/5 border border-white/5 space-y-1.5">
                    <div class="text-theme-primary font-bold text-sm">Express Delivery Add-on</div>
                    <div class="text-white font-semibold">Within 24 to 48 Hours</div>
                    <p class="text-zinc-400 text-[11px]">Optional priority turnaround available upon request for urgent press releases and editorial campaigns.</p>
                </div>
            </div>
        </section>

        <section class="space-y-3">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="text-theme-primary font-mono">03.</span>
                <span>Physical Products Shipping & Delivery (Pan-India)</span>
            </h2>
            <p>For packages and orders that include physical handcrafted albums, prints, or photo books:</p>
            <ul class="list-disc list-inside space-y-2 pl-2 text-zinc-400">
                <li>
                    <strong class="text-white">Production & Crafting Window:</strong> Premium leather layflat albums and museum-grade framed prints require 10 to 14 business days of precision printing and binding following client design approval.
                </li>
                <li>
                    <strong class="text-white">Shipping & Transit Duration:</strong> Once crafted, physical products are securely boxed in protective archival packaging and dispatched via insured courier partners (e.g., Blue Dart, DTDC, Delhivery). Delivery typically takes <span class="text-emerald-400 font-semibold">3 to 7 business days</span> depending on the delivery pincode in India.
                </li>
                <li>
                    <strong class="text-white">Tracking & Dispatch Notification:</strong> Clients will receive an SMS and email notification with the courier tracking ID as soon as the package is dispatched.
                </li>
                <li>
                    <strong class="text-white">Shipping Charges:</strong> Standard domestic delivery is complimentary for all signature luxury packages. International shipping rates are calculated dynamically based on destination.
                </li>
            </ul>
        </section>

        <section class="space-y-3">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="text-theme-primary font-mono">04.</span>
                <span>Damaged or Defective Physical Items</span>
            </h2>
            <p>
                All physical shipments undergo strict quality checks before dispatch. In the rare event that an album or print arrives damaged in transit, please notify us within <strong class="text-white">48 hours of delivery</strong> with unboxing photos or video. We will reprint and replace damaged items at zero additional cost.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="text-theme-primary font-mono">05.</span>
                <span>Delivery Support & Inquiries</span>
            </h2>
            <p>For inquiries regarding your delivery status or tracking information, please contact our dispatch team:</p>
            <div class="p-4 rounded-2xl bg-white/5 border border-white/5 space-y-1.5 text-zinc-300">
                <p><strong class="text-white">{{ $siteSettings['site_name'] ?? 'Middukhera Production' }}</strong></p>
                <p>Email: <a href="mailto:{{ $siteSettings['contact_email'] ?? 'contact@middukheraproduction.in' }}" class="text-theme-primary hover:underline">{{ $siteSettings['contact_email'] ?? 'contact@middukheraproduction.in' }}</a></p>
                <p>Phone: <a href="tel:{{ $siteSettings['contact_phone'] ?? '+919876543210' }}" class="text-theme-primary hover:underline">{{ $siteSettings['contact_phone'] ?? '+91 98765 43210' }}</a></p>
                <p>Studio Address: {{ $siteSettings['contact_address'] ?? 'Middukhera Production Studio, India' }}</p>
                <p>Operating Hours: {{ $siteSettings['operating_hours'] ?? 'Mon - Sun: 09:00 AM - 09:00 PM IST' }}</p>
            </div>
        </section>
    </div>
</div>
@endsection
