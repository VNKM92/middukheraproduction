@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-12 px-4 sm:px-6 lg:px-8" x-data="checkoutEngine()">
    
    <!-- Top Step Bar -->
    <div class="mb-10 text-center">
        <span class="text-xs font-bold uppercase tracking-widest text-theme-primary">Secure Reservation &bull; Step 1 of 2</span>
        <h1 class="text-3xl sm:text-4xl font-serif font-bold text-white mt-1">Reserve Your Studio Session</h1>
        <div class="w-16 h-0.5 bg-theme-primary mx-auto mt-3 mb-2"></div>
        <p class="text-xs text-zinc-400">Review your package specifications, verify your contact number, and select your preferred shoot date.</p>
    </div>

    @if(session('error'))
        <div class="mb-6 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-xs flex items-center gap-3">
            <i data-lucide="alert-octagon" class="w-5 h-5 text-rose-400 shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left: Booking Form -->
        <div class="lg:col-span-7 site-card rounded-3xl border border-white/10 p-6 sm:p-8 space-y-6 shadow-2xl">
            <h2 class="text-base font-bold text-white flex items-center gap-2 border-b border-white/10 pb-3">
                <i data-lucide="calendar" class="w-5 h-5 text-theme-primary"></i>
                <span>Reservation Details</span>
            </h2>

            <form action="{{ route('booking.store') }}" method="POST" class="space-y-6" @submit="handleSubmit($event)">
                @csrf
                <input type="hidden" name="package_id" value="{{ $package->id }}" />
                <input type="hidden" name="otp_token" :value="otpToken" />

                <!-- Contact Information Section -->
                <div class="space-y-4 p-5 rounded-2xl bg-white/5 border border-white/10">
                    <div class="flex items-center justify-between">
                        <div class="text-xs font-bold uppercase tracking-wider text-theme-primary flex items-center gap-1.5">
                            <i data-lucide="user-check" class="w-4 h-4"></i>
                            <span>Client Identification & SMS Alert</span>
                        </div>
                        <span x-show="isPhoneVerified" class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center gap-1">
                            <i data-lucide="shield-check" class="w-3 h-3"></i> Verified Phone
                        </span>
                    </div>

                    @guest
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-xs font-semibold text-zinc-300">Full Name <span class="text-rose-400">*</span></label>
                                <input type="text" name="client_name" x-model="clientName" required value="{{ old('client_name') }}" placeholder="e.g. Maya Sharma" class="w-full px-4 py-2.5 rounded-xl bg-black/40 border border-white/10 text-white text-xs focus:border-theme-primary">
                            </div>

                            <div class="space-y-1">
                                <label class="text-xs font-semibold text-zinc-300">Email Address <span class="text-rose-400">*</span></label>
                                <input type="email" name="client_email" x-model="clientEmail" required value="{{ old('client_email') }}" placeholder="maya@example.com" class="w-full px-4 py-2.5 rounded-xl bg-black/40 border border-white/10 text-white text-xs focus:border-theme-primary">
                            </div>
                        </div>
                    @endguest

                    <!-- Phone Input with OTP Verification Trigger -->
                    <div class="space-y-2">
                        <label class="text-xs font-semibold text-zinc-300 flex items-center justify-between">
                            <span>Mobile Number (For Custom SMS & Booking Tracking) <span class="text-rose-400">*</span></span>
                            <span class="text-[10px] text-zinc-400">Indian 10-digit or International E.164</span>
                        </label>
                        
                        <div class="flex items-center gap-2">
                            <div class="relative flex-1">
                                <input type="text" name="client_phone" x-model="clientPhone" :disabled="isPhoneVerified" placeholder="+91 98765 43210" required class="w-full px-4 py-2.5 rounded-xl bg-black/40 border border-white/10 text-white text-xs focus:border-theme-primary disabled:opacity-60 disabled:cursor-not-allowed">
                                <span x-show="isPhoneVerified" class="absolute right-3 top-2.5 text-emerald-400">
                                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                                </span>
                            </div>

                            <button type="button" @click="requestOtp()" :disabled="isOtpSending || isPhoneVerified || !clientPhone" class="px-4 py-2.5 rounded-xl text-xs font-bold shrink-0 transition flex items-center gap-1.5" :class="isPhoneVerified ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-white/10 hover:bg-theme-primary hover:text-black text-white border border-white/10'">
                                <i data-lucide="smartphone" class="w-3.5 h-3.5"></i>
                                <span x-text="isOtpSending ? 'Sending SMS...' : (isPhoneVerified ? 'Verified' : 'Verify via OTP')"></span>
                            </button>
                        </div>

                        <!-- Interactive OTP Verification Modal / Box -->
                        <div x-show="showOtpBox && !isPhoneVerified" x-transition class="p-4 rounded-xl bg-theme-primary/10 border border-[var(--theme-primary)]/30 space-y-3 mt-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-white flex items-center gap-1.5">
                                    <i data-lucide="key-round" class="w-4 h-4 text-theme-primary"></i>
                                    <span>Enter 6-Digit SMS Verification Code</span>
                                </span>
                                <span class="text-[11px] text-zinc-400" x-show="timer > 0">Resend in <strong class="text-theme-primary" x-text="timer + 's'"></strong></span>
                            </div>

                            <p class="text-[11px] text-zinc-300">
                                We sent a secure verification code to <span class="font-mono font-bold text-white" x-text="clientPhone"></span>.
                            </p>

                            <!-- Test OTP code hint in debug / simulation mode -->
                            <template x-if="simulatedOtp">
                                <div class="p-2 rounded-lg bg-black/50 border border-amber-500/30 text-amber-300 text-[11px] flex items-center justify-between">
                                    <span>Simulation Mode OTP: <strong class="font-mono text-white tracking-widest" x-text="simulatedOtp"></strong></span>
                                    <button type="button" @click="otpCode = simulatedOtp" class="px-2 py-0.5 rounded bg-amber-500/20 text-[10px] font-bold text-amber-200 hover:bg-amber-500/30">Auto-fill</button>
                                </div>
                            </template>

                            <div class="flex items-center gap-2">
                                <input type="text" x-model="otpCode" maxlength="6" placeholder="123456" class="w-36 text-center tracking-[0.25em] font-mono text-base font-bold px-3 py-2 rounded-xl bg-black/60 border border-white/20 text-white focus:border-theme-primary">
                                
                                <button type="button" @click="verifyOtp()" :disabled="isOtpVerifying || otpCode.length < 4" class="px-4 py-2 rounded-xl text-xs font-bold btn-gold-dynamic shadow-md flex items-center gap-1">
                                    <i data-lucide="check" class="w-3.5 h-3.5"></i>
                                    <span x-text="isOtpVerifying ? 'Verifying...' : 'Submit OTP'"></span>
                                </button>

                                <button type="button" @click="requestOtp()" :disabled="timer > 0 || isOtpSending" class="px-3 py-2 text-xs text-zinc-400 hover:text-white disabled:opacity-40">
                                    Resend
                                </button>
                            </div>

                            <div x-show="otpError" class="text-[11px] text-rose-400 font-medium" x-text="otpError"></div>
                        </div>
                    </div>
                </div>

                <!-- Booking Date -->
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-zinc-300 flex items-center justify-between">
                        <span>Desired Shoot Date <span class="text-rose-400">*</span></span>
                        <span class="text-[10px] text-zinc-400">Rescheduling available up to 5 days prior</span>
                    </label>
                    <input type="date" name="booking_date" min="{{ date('Y-m-d') }}" required value="{{ old('booking_date', date('Y-m-d', strtotime('+3 days'))) }}" class="w-full px-4 py-3 rounded-xl bg-black/40 border border-white/10 text-white text-sm focus:border-theme-primary cursor-pointer">
                </div>

                <!-- Custom Amount or Deposit -->
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-zinc-300 flex items-center justify-between">
                        <span>Investment Amount ({{ $siteSettings['currency_symbol'] ?? '₹' }}) <span class="text-rose-400">*</span></span>
                        <span class="text-[10px] text-zinc-400">Tier Range: {{ $siteSettings['currency_symbol'] ?? '₹' }}{{ number_format($package->price_min) }} - {{ number_format($package->price_max) }}</span>
                    </label>
                    <input type="number" name="amount" min="{{ $package->price_min }}" max="{{ $package->price_max }}" value="{{ old('amount', $package->price_min) }}" required class="w-full px-4 py-3 rounded-xl bg-black/40 border border-white/10 text-white text-sm font-bold focus:border-theme-primary">
                </div>

                <!-- Special Notes / Wardrobe Requirements -->
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-zinc-300">Creative Notes or Specific Requests (Optional)</label>
                    <textarea name="notes" rows="3" placeholder="Tell us about your moodboard ideas, location preference (in-studio or on-location), or wardrobe styling needs..." class="w-full px-4 py-3 rounded-xl bg-black/40 border border-white/10 text-white text-xs leading-relaxed focus:border-theme-primary">{{ old('notes') }}</textarea>
                </div>

                <div class="pt-4 border-t border-white/5 space-y-3">
                    <button type="submit" class="w-full py-4 rounded-full font-bold text-xs uppercase tracking-wider btn-gold-dynamic shadow-xl shadow-[var(--theme-primary)]/25 flex items-center justify-center gap-2 hover:scale-[1.01] transition duration-300">
                        <i data-lucide="lock" class="w-4 h-4"></i>
                        <span>Proceed to Razorpay Secure Gateway</span>
                    </button>
                    <p class="text-center text-[11px] text-zinc-400 flex items-center justify-center gap-1">
                        <i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald-400"></i>
                        <span>Razorpay Encrypted &bull; Webhook & Transaction Tracking Enabled</span>
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
                        <span>Studio Concierge & SMS Alerts:</span>
                        <span class="text-emerald-400 font-semibold">Active</span>
                    </div>
                    <div class="flex items-center justify-between text-zinc-400">
                        <span>Live Transaction Tracking:</span>
                        <span class="text-cyan-400 font-semibold">Enabled</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
function checkoutEngine() {
    return {
        clientName: '{{ Auth::user()->name ?? old('client_name') }}',
        clientEmail: '{{ Auth::user()->email ?? old('client_email') }}',
        clientPhone: '{{ old('client_phone') }}',
        showOtpBox: false,
        isOtpSending: false,
        isOtpVerifying: false,
        isPhoneVerified: false,
        otpCode: '',
        otpToken: '',
        simulatedOtp: null,
        otpError: '',
        timer: 0,
        timerInterval: null,

        startTimer(duration = 60) {
            this.timer = duration;
            clearInterval(this.timerInterval);
            this.timerInterval = setInterval(() => {
                if (this.timer > 0) {
                    this.timer--;
                } else {
                    clearInterval(this.timerInterval);
                }
            }, 1000);
        },

        async requestOtp() {
            if (!this.clientPhone || this.clientPhone.trim().length < 8) {
                alert('Please enter a valid mobile number first.');
                return;
            }

            this.isOtpSending = true;
            this.otpError = '';

            try {
                const response = await fetch('{{ route('otp.send') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        phone: this.clientPhone,
                        email: this.clientEmail,
                        name: this.clientName,
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.showOtpBox = true;
                    this.otpToken = data.token;
                    this.simulatedOtp = data.simulated_otp;
                    this.startTimer(data.cooldown || 60);
                } else {
                    this.otpError = data.message || 'Unable to send OTP at this time.';
                    if (data.token) this.otpToken = data.token;
                    if (data.cooldown) this.startTimer(data.cooldown);
                    this.showOtpBox = true;
                }
            } catch (err) {
                this.otpError = 'Network error requesting OTP. Please check connection.';
            } finally {
                this.isOtpSending = false;
                if (window.lucide) window.lucide.createIcons();
            }
        },

        async verifyOtp() {
            if (!this.otpToken || !this.otpCode) return;

            this.isOtpVerifying = true;
            this.otpError = '';

            try {
                const response = await fetch('{{ route('otp.verify') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        token: this.otpToken,
                        otp: this.otpCode,
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.isPhoneVerified = true;
                    this.showOtpBox = false;
                } else {
                    this.otpError = data.message || 'Verification failed. Incorrect OTP.';
                }
            } catch (err) {
                this.otpError = 'Network error verifying OTP.';
            } finally {
                this.isOtpVerifying = false;
                if (window.lucide) window.lucide.createIcons();
            }
        },

        handleSubmit(e) {
            // If phone verification is strictly required and user has not verified yet
            @if(Setting::get('otp_verification_required', '0') == '1')
                if (!this.isPhoneVerified) {
                    e.preventDefault();
                    alert('Please verify your mobile number via OTP before proceeding to payment.');
                    if (!this.showOtpBox) {
                        this.requestOtp();
                    }
                    return;
                }
            @endif
        }
    }
}
</script>
@endsection
