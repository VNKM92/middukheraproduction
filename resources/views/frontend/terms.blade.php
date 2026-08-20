@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-16 px-4 sm:px-6 lg:px-8 space-y-10">
    <div class="text-center space-y-3">
        <span class="text-xs font-bold uppercase tracking-widest text-theme-primary">Legal & Compliance</span>
        <h1 class="text-3xl sm:text-4xl font-serif font-bold text-white">Terms of Studio Engagement & Service</h1>
        <div class="w-16 h-0.5 bg-theme-primary mx-auto mt-3 mb-2"></div>
        <p class="text-xs text-zinc-400">Last updated: {{ date('F Y') }} &bull; {{ $siteSettings['site_name'] ?? 'Middukhera Studio & Productions' }}</p>
    </div>

    <div class="site-card rounded-3xl border border-white/10 p-8 sm:p-12 space-y-8 text-xs text-zinc-300 leading-relaxed shadow-2xl">
        <section class="space-y-3">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="text-theme-primary font-mono">01.</span>
                <span>Booking & Reservation Deposits</span>
            </h2>
            <p>
                To reserve a photoshoot session with {{ $siteSettings['site_name'] ?? 'our studio' }}, a booking fee or deposit must be authorized through our integrated Razorpay payment gateway. Reservations are confirmed immediately upon electronic transaction capture.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="text-theme-primary font-mono">02.</span>
                <span>Rescheduling & Cancellation Policy</span>
            </h2>
            <p>
                Clients may reschedule scheduled studio or on-location sessions up to five (5) calendar days before the agreed shoot date with zero penalty. Cancellations initiated within 48 hours of the shoot time may be subject to a nominal production preparation retainer fee.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="text-theme-primary font-mono">03.</span>
                <span>Creative Direction & Artistic Discretion</span>
            </h2>
            <p>
                Our principal photographers and creative directors retain artistic discretion regarding camera angles, lighting styling, and high-fidelity post-production color grading. We commit to working diligently within the client’s approved moodboard brief.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="text-theme-primary font-mono">04.</span>
                <span>Intellectual Property & Licensing Rights</span>
            </h2>
            <p>
                Clients receive non-exclusive, perpetual personal printing and digital usage rights for all delivered high-resolution plates. Commercial and advertising usage rights are granted according to the specific commercial tier selected.
            </p>
        </section>

        <section class="space-y-3">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="text-theme-primary font-mono">05.</span>
                <span>Deliverable Timelines</span>
            </h2>
            <p>
                Contact sheets are made accessible in your Client Dashboard within 48-72 hours. Master retouched digital files are delivered within 7 to 14 business days.
            </p>
        </section>
    </div>
</div>
@endsection
