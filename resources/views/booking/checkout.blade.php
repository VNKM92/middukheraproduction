@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    
    <!-- Top Step Bar -->
    <div class="mb-10 text-center">
        <span class="text-xs font-bold uppercase tracking-widest text-theme-primary">Secure Reservation</span>
        <h1 class="text-3xl sm:text-4xl font-serif font-bold text-white mt-1">Reserve Your Studio Session</h1>
        <div class="w-16 h-0.5 bg-theme-primary mx-auto mt-3 mb-2"></div>
        <p class="text-xs text-zinc-400">Review your package specifications and select your preferred shoot date.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left: Booking Form -->
        <div class="lg:col-span-7 site-card rounded-3xl border border-white/10 p-6 sm:p-8 space-y-6 shadow-2xl">
            <h2 class="text-base font-bold text-white flex items-center gap-2 border-b border-white/10 pb-3">
                <i data-lucide="calendar" class="w-5 h-5 text-theme-primary"></i>
                <span>Reservation Details</span>
            </h2>

            <form action="{{ route('booking.store') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="package_id" value="{{ $package->id }}" />

                @guest
                    <!-- Guest Contact Fields -->
                    <div class="space-y-4 p-4 rounded-2xl bg-white/5 border border-white/5">
                        <div class="text-xs font-bold uppercase tracking-wider text-theme-primary flex items-center gap-1.5">
                            <i data-lucide="user-check" class="w-4 h-4"></i>
                            <span>Client Contact Information</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-xs font-semibold text-zinc-300">Full Name</label>
                                <input type="text" name="client_name" required value="{{ old('client_name') }}" placeholder="e.g. Maya Sharma" class="w-full px-4 py-2.5 rounded-xl bg-black/40 border border-white/10 text-white text-xs focus:border-theme-primary">
                            </div>

                            <div class="space-y-1">
                                <label class="text-xs font-semibold text-zinc-300">Email Address</label>
                                <input type="email" name="client_email" required value="{{ old('client_email') }}" placeholder="maya@example.com" class="w-full px-4 py-2.5 rounded-xl bg-black/40 border border-white/10 text-white text-xs focus:border-theme-primary">
                            </div>

                            <div class="space-y-1 sm:col-span-2">
                                <label class="text-xs font-semibold text-zinc-300">Phone / WhatsApp Number</label>
                                <input type="text" name="client_phone" value="{{ old('client_phone') }}" placeholder="+91 98765 43210" class="w-full px-4 py-2.5 rounded-xl bg-black/40 border border-white/10 text-white text-xs focus:border-theme-primary">
                            </div>
                        </div>

                        <div class="text-[11px] text-zinc-400">
                            Already have an account? <a href="{{ route('login') }}" class="text-theme-primary hover:underline font-semibold">Sign in here</a>
                        </div>
                    </div>
                @endguest

                <!-- Booking Date -->
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-zinc-300 flex items-center justify-between">
                        <span>Desired Shoot Date</span>
                        <span class="text-[10px] text-zinc-400">Rescheduling available up to 5 days prior</span>
                    </label>
                    <input type="date" name="booking_date" min="{{ date('Y-m-d') }}" required value="{{ old('booking_date', date('Y-m-d', strtotime('+3 days'))) }}" class="w-full px-4 py-3 rounded-xl bg-black/40 border border-white/10 text-white text-sm focus:border-theme-primary cursor-pointer">
                </div>

                <!-- Custom Amount or Deposit -->
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-zinc-300 flex items-center justify-between">
                        <span>Investment Amount ({{ $siteSettings['currency_symbol'] ?? '₹' }})</span>
                        <span class="text-[10px] text-zinc-400">Tier Range: {{ $siteSettings['currency_symbol'] ?? '₹' }}{{ number_format($package->price_min) }} - {{ number_format($package->price_max) }}</span>
                    </label>
                    <input type="number" name="amount" min="{{ $package->price_min }}" max="{{ $package->price_max }}" value="{{ old('amount', $package->price_min) }}" required class="w-full px-4 py-3 rounded-xl bg-black/40 border border-white/10 text-white text-sm font-bold focus:border-theme-primary">
                </div>

                <!-- Special Notes / Wardrobe Requirements -->
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-zinc-300">Creative Notes or Specific Requests (Optional)</label>
                    <textarea name="notes" rows="3" placeholder="Tell us about your moodboard ideas, location preference (in-studio or on-location), or wardrobe styling needs..." class="w-full px-4 py-3 rounded-xl bg-black/40 border border-white/10 text-white text-xs leading-relaxed focus:border-theme-primary">{{ old('notes') }}</textarea>
                </div>

                <div class="pt-4 border-t border-white/5">
                    <button type="submit" class="w-full py-4 rounded-full font-bold text-xs uppercase tracking-wider btn-gold-dynamic shadow-xl shadow-[var(--theme-primary)]/25 flex items-center justify-center gap-2 hover:scale-[1.01] transition duration-300">
                        <i data-lucide="lock" class="w-4 h-4"></i>
                        <span>Proceed to Secure Payment</span>
                    </button>
                    <p class="text-center text-[11px] text-zinc-400 mt-2 flex items-center justify-center gap-1">
                        <i data-lucide="shield" class="w-3.5 h-3.5 text-emerald-400"></i>
                        <span>256-Bit SSL Encrypted &bull; Razorpay Verified Checkout</span>
                    </p>
                </div>
            </form>
        </div>

        <!-- Right: Package Summary Card -->
        <div class="lg:col-span-5 site-card rounded-3xl border border-white/10 p-6 sm:p-8 space-y-6 shadow-2xl">
            <div class="aspect-video w-full rounded-2xl overflow-hidden relative border border-white/10">
                <img src="{{ $package->image_path ?: 'https://images.unsplash.com/photo-1492691527719-9d1e07e534b4?w=800' }}" alt="{{ $package->name }}" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                <div class="absolute bottom-3 left-4 right-4">
                    <span class="text-[10px] font-bold uppercase tracking-widest px-2 py-0.5 rounded bg-theme-primary text-black">Selected Package</span>
                    <h3 class="text-lg font-serif font-bold text-white mt-1">{{ $package->name }}</h3>
                </div>
            </div>

            <div class="space-y-4">
                <p class="text-xs text-zinc-400 leading-relaxed">{!! strip_tags($package->description) !!}</p>

                @php
                    $features = is_array($package->features) ? $package->features : json_decode($package->features, true) ?? explode(',', $package->features);
                @endphp

                @if(!empty($features))
                    <div class="space-y-2 pt-2 border-t border-white/5">
                        <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-300">Included Deliverables:</div>
                        <ul class="space-y-2 text-xs text-zinc-300">
                            @foreach($features as $feat)
                                <li class="flex items-start gap-2">
                                    <i data-lucide="check-circle-2" class="w-4 h-4 text-theme-primary shrink-0 mt-0.5"></i>
                                    <span>{{ trim($feat) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="p-4 rounded-2xl bg-white/5 border border-white/5 space-y-2 text-xs">
                    <div class="flex items-center justify-between text-zinc-400">
                        <span>Base Package Rate:</span>
                        <span class="text-white font-semibold">{{ $siteSettings['currency_symbol'] ?? '₹' }}{{ number_format($package->price_min) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-zinc-400">
                        <span>Studio Concierge & Briefing:</span>
                        <span class="text-emerald-400 font-semibold">Included</span>
                    </div>
                    <div class="flex items-center justify-between text-zinc-400">
                        <span>Color Grading & Edits:</span>
                        <span class="text-emerald-400 font-semibold">Included</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
