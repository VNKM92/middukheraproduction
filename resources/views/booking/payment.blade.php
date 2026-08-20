@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-16 px-4 sm:px-6 lg:px-8">
    <div class="site-card rounded-3xl border border-white/10 p-8 sm:p-10 space-y-8 shadow-2xl text-center">
        
        <!-- Status Icon -->
        <div class="w-16 h-16 rounded-2xl bg-theme-primary/15 border border-[var(--theme-primary)]/30 mx-auto flex items-center justify-center text-theme-primary shadow-lg shadow-[var(--theme-primary)]/20">
            <i data-lucide="credit-card" class="w-8 h-8"></i>
        </div>

        <div class="space-y-2">
            <span class="text-xs font-bold uppercase tracking-widest text-theme-primary">Payment Confirmation</span>
            <h1 class="text-3xl font-serif font-bold text-white">Complete Booking Deposit</h1>
            <p class="text-xs text-zinc-400">Order Reference: <strong class="text-white font-mono">#{{ $booking->id }}</strong></p>
        </div>

        <!-- Warning Alert if applicable -->
        @if(!empty($warning))
            <div class="p-4 rounded-xl site-card border border-amber-500/30 text-amber-300 text-xs text-left flex items-start gap-2.5">
                <i data-lucide="alert-circle" class="w-4 h-4 shrink-0 mt-0.5 text-amber-400"></i>
                <span>{{ $warning }}</span>
            </div>
        @endif

        <!-- Order Summary Receipt Box -->
        <div class="p-6 rounded-2xl bg-white/5 border border-white/5 space-y-3 text-left text-xs">
            <div class="flex items-center justify-between">
                <span class="text-zinc-400">Selected Package:</span>
                <span class="font-bold text-white">{{ $booking->package->name ?? 'Photoshoot Package' }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-zinc-400">Shoot Scheduled Date:</span>
                <span class="font-bold text-white">{{ \Carbon\Carbon::parse($booking->booking_date)->format('l, F j, Y') }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-zinc-400">Client Account:</span>
                <span class="font-bold text-white">{{ $booking->user->name ?? 'Client' }} ({{ $booking->user->email ?? '' }})</span>
            </div>
            <div class="pt-3 border-t border-white/10 flex items-center justify-between text-sm">
                <span class="font-bold text-white">Total Amount Due:</span>
                <span class="font-bold text-xl text-emerald-400">{{ $siteSettings['currency_symbol'] ?? '₹' }}{{ number_format($booking->amount) }}</span>
            </div>
        </div>

        <!-- Payment Actions -->
        @if($isMock)
            <div class="space-y-4 pt-2">
                <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-xs leading-relaxed">
                    <strong>Demo / Sandbox Checkout Active:</strong> Click the button below to instantly simulate a successful Razorpay payment capture and confirm your booking.
                </div>

                <form method="POST" action="{{ route('booking.callback') }}">
                    @csrf
                    <input type="hidden" name="booking_id" value="{{ $booking->id }}" />
                    <input type="hidden" name="mock_payment" value="1" />
                    <button type="submit" class="w-full py-4 rounded-full font-bold text-xs uppercase tracking-wider btn-gold-dynamic shadow-xl shadow-[var(--theme-primary)]/20 flex items-center justify-center gap-2 hover:scale-[1.01] transition">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        <span>Confirm & Simulate Instant Payment</span>
                    </button>
                </form>
            </div>
        @else
            <div class="pt-2">
                <button id="rzp-button" class="w-full py-4 rounded-full font-bold text-xs uppercase tracking-wider btn-gold-dynamic shadow-xl shadow-[var(--theme-primary)]/20 flex items-center justify-center gap-2 hover:scale-[1.01] transition">
                    <i data-lucide="shield-check" class="w-4 h-4"></i>
                    <span>Pay {{ $siteSettings['currency_symbol'] ?? '₹' }}{{ number_format($booking->amount) }} via Razorpay</span>
                </button>

                <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
                <script>
                    const rzpOptions = {
                        key: "{{ $keyId }}",
                        amount: "{{ $booking->amount * 100 }}",
                        currency: "INR",
                        name: "{{ $siteSettings['site_name'] ?? 'Lumina Studio' }}",
                        description: "Photoshoot Booking #{{ $booking->id }} - {{ $booking->package->name ?? 'Package' }}",
                        image: "{{ $siteSettings['hero_bg_image'] ?? '' }}",
                        order_id: "{{ $booking->razorpay_order_id }}",
                        prefill: {
                            name: "{{ $booking->user->name ?? '' }}",
                            email: "{{ $booking->user->email ?? '' }}",
                        },
                        theme: {
                            color: "{{ $siteSettings['primary_color'] ?? '#E5C158' }}"
                        },
                        handler: function (response) {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = '{{ route('booking.callback') }}';
                            
                            const token = document.createElement('input'); token.type = 'hidden'; token.name = '_token'; token.value = '{{ csrf_token() }}'; form.appendChild(token);
                            const bid = document.createElement('input'); bid.type = 'hidden'; bid.name = 'booking_id'; bid.value = '{{ $booking->id }}'; form.appendChild(bid);
                            const pid = document.createElement('input'); pid.type = 'hidden'; pid.name = 'razorpay_payment_id'; pid.value = response.razorpay_payment_id; form.appendChild(pid);
                            const oid = document.createElement('input'); oid.type = 'hidden'; oid.name = 'razorpay_order_id'; oid.value = response.razorpay_order_id; form.appendChild(oid);
                            const sig = document.createElement('input'); sig.type = 'hidden'; sig.name = 'razorpay_signature'; sig.value = response.razorpay_signature; form.appendChild(sig);
                            
                            document.body.appendChild(form);
                            form.submit();
                        }
                    };

                    const rzp = new Razorpay(rzpOptions);
                    document.getElementById('rzp-button').onclick = function(e) {
                        e.preventDefault();
                        rzp.open();
                    };
                </script>
            </div>
        @endif

        <div class="pt-4 border-t border-white/5 flex items-center justify-center gap-4 text-xs text-zinc-400">
            <a href="{{ route('home') }}" class="hover:text-white transition">&larr; Return to Home</a>
            <span>&bull;</span>
            <a href="{{ route('contact') }}" class="hover:text-white transition">Need Assistance?</a>
        </div>
    </div>
</div>
@endsection
