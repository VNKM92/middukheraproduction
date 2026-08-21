@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-16 px-4 sm:px-6 lg:px-8 space-y-10">
    <div class="text-center space-y-3">
        <span class="text-xs font-bold uppercase tracking-widest text-theme-primary">Legal & Compliance</span>
        <h1 class="text-3xl sm:text-4xl font-serif font-bold text-white">Cancellation & Refund Policy</h1>
        <div class="w-16 h-0.5 bg-theme-primary mx-auto mt-3 mb-2"></div>
        <p class="text-xs text-zinc-400">Effective Date: {{ date('F Y') }} &bull; {{ $siteSettings['site_name'] ?? 'Middukhera Production' }}</p>
    </div>

    <div class="site-card rounded-3xl border border-white/10 p-8 sm:p-12 space-y-8 text-xs text-zinc-300 leading-relaxed shadow-2xl">
        <section class="space-y-3">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="text-theme-primary font-mono">01.</span>
                <span>Overview</span>
            </h2>
            <p>
                At <strong>{{ $siteSettings['site_name'] ?? 'Middukhera Production' }}</strong>, we strive to deliver an exceptional photography and media production experience. Because our sessions involve dedicated studio reservations, equipment prep, and specialized crew scheduling, we maintain a transparent and balanced cancellation and refund policy.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="text-theme-primary font-mono">02.</span>
                <span>Session Cancellation Windows</span>
            </h2>
            <p>If you need to cancel a confirmed photoshoot or production booking, the following terms apply:</p>
            <ul class="list-disc list-inside space-y-2 pl-2 text-zinc-400">
                <li>
                    <strong class="text-white">Cancellation 7 or more days prior to scheduled shoot date:</strong> You will be eligible for a <span class="text-emerald-400 font-semibold">100% full refund</span> of the amount paid.
                </li>
                <li>
                    <strong class="text-white">Cancellation between 3 to 6 days prior to scheduled shoot date:</strong> You will be eligible for an <span class="text-emerald-400 font-semibold">80% refund</span> (a 20% administrative and slot reservation charge applies).
                </li>
                <li>
                    <strong class="text-white">Cancellation within 48 hours of scheduled shoot date:</strong> A 50% retainer fee is retained to cover non-recoverable studio booking and crew reservation costs; the remaining 50% will be refunded.
                </li>
                <li>
                    <strong class="text-white">Same-day Cancellation or No-Show:</strong> In case of a no-show on the day of the shoot without prior notice, no refund will be issued. However, one-time rescheduling may be accommodated under exceptional circumstances at the studio's discretion.
                </li>
            </ul>
        </section>

        <section class="space-y-3">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="text-theme-primary font-mono">03.</span>
                <span>Free Rescheduling Policy</span>
            </h2>
            <p>
                We understand that emergencies and unforeseen weather events happen. You may request to reschedule your photoshoot session up to <strong class="text-white">5 calendar days prior</strong> to the scheduled date at <strong class="text-emerald-400">zero extra cost</strong>. Rescheduled dates must be within 90 days of the original booking date, subject to studio and photographer availability.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="text-theme-primary font-mono">04.</span>
                <span>Refund Processing Timeline</span>
            </h2>
            <div class="p-5 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-zinc-200 space-y-2">
                <p class="font-bold text-emerald-400 text-sm flex items-center gap-2">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                    <span>Mandatory Refund Timeline (Razorpay Standard)</span>
                </p>
                <p>
                    All eligible refunds approved by {{ $siteSettings['site_name'] ?? 'Middukhera Production' }} will be processed electronically through our payment gateway (Razorpay). 
                </p>
                <p class="font-medium text-white">
                    &bull; Approved refunds will be credited back to the <span class="text-theme-primary">original payment method</span> (Credit/Debit Card, Net Banking, UPI, or Wallet) within <span class="text-emerald-400 font-bold">5 to 7 working days</span> from the date of refund approval.
                </p>
            </div>
        </section>

        <section class="space-y-3">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="text-theme-primary font-mono">05.</span>
                <span>Studio-Initiated Cancellations</span>
            </h2>
            <p>
                In the rare event that {{ $siteSettings['site_name'] ?? 'Middukhera Production' }} must cancel a scheduled booking due to equipment malfunction, severe weather, health emergencies, or unforeseen operational constraints, you will be offered an immediate choice of:
            </p>
            <ul class="list-disc list-inside space-y-1.5 pl-2 text-zinc-400">
                <li>A prompt 100% full refund processed within 5 to 7 working days.</li>
                <li>Priority rescheduling with a complimentary upgrade or bonus retouched deliverables.</li>
            </ul>
        </section>

        <section class="space-y-3">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="text-theme-primary font-mono">06.</span>
                <span>How to Request a Cancellation or Refund</span>
            </h2>
            <p>To request a cancellation, reschedule, or refund, please submit your request along with your Booking ID and payment receipt details to:</p>
            <div class="p-4 rounded-2xl bg-white/5 border border-white/5 space-y-1.5 text-zinc-300">
                <p><strong class="text-white">Refund Desk & Concierge:</strong></p>
                <p>Email: <a href="mailto:{{ $siteSettings['contact_email'] ?? 'contact@middukheraproduction.in' }}" class="text-theme-primary hover:underline">{{ $siteSettings['contact_email'] ?? 'contact@middukheraproduction.in' }}</a></p>
                <p>Phone / WhatsApp: <a href="tel:{{ $siteSettings['contact_phone'] ?? '+919876543210' }}" class="text-theme-primary hover:underline">{{ $siteSettings['contact_phone'] ?? '+91 98765 43210' }}</a></p>
                <p>Studio Address: {{ $siteSettings['contact_address'] ?? 'Middukhera Production Studio, India' }}</p>
                <p>Operating Hours: {{ $siteSettings['operating_hours'] ?? 'Mon - Sun: 09:00 AM - 09:00 PM IST' }}</p>
            </div>
        </section>
    </div>
</div>
@endsection
