@extends('layouts.app')

@section('content')
<div class="relative overflow-hidden py-16 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto relative z-10 space-y-16">
        
        <!-- Header Title -->
        <div class="text-center max-w-3xl mx-auto space-y-4 reveal">
            <span class="text-xs font-bold uppercase tracking-widest text-theme-primary">VIP Studio Concierge</span>
            <h1 class="font-serif text-4xl sm:text-5xl font-bold text-white">
                Commission Our Creative Studio
            </h1>
            <div class="w-16 h-0.5 bg-theme-primary mx-auto mt-3 mb-2"></div>
            <p class="text-xs sm:text-sm text-zinc-400 max-w-xl mx-auto leading-relaxed">
                Let us frame your vision. Fill out our signature inquiry form below, or reach out directly to our concierge booking desk.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
            
            <!-- Left: Studio Details & Direct Channels -->
            <div class="lg:col-span-5 space-y-6 reveal">
                <div class="site-card rounded-3xl p-8 border border-white/10 space-y-6 shadow-2xl">
                    <h3 class="font-serif text-2xl font-bold text-white">Direct Channels</h3>
                    
                    <div class="space-y-5 pt-2 text-xs">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-theme-primary/10 border border-theme-primary/20 flex items-center justify-center text-theme-primary shrink-0">
                                <i data-lucide="phone" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="text-[10px] uppercase tracking-widest text-zinc-400 font-semibold">Concierge Phone</h4>
                                <a href="tel:{{ $siteSettings['contact_phone'] ?? '+919876543210' }}" class="text-sm font-bold text-white hover:text-[var(--theme-primary)] transition block mt-0.5">{{ $siteSettings['contact_phone'] ?? '+91 98765 43210' }}</a>
                                <span class="text-[11px] text-zinc-500 font-light">{{ $siteSettings['operating_hours'] ?? 'Mon - Sun: 09:00 AM - 09:00 PM IST' }}</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400 shrink-0">
                                <i data-lucide="mail" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="text-[10px] uppercase tracking-widest text-zinc-400 font-semibold">Concierge Email</h4>
                                <a href="mailto:{{ $siteSettings['contact_email'] ?? 'contact@middukheraproduction.in' }}" class="text-sm font-bold text-white hover:text-[var(--theme-primary)] transition block mt-0.5">{{ $siteSettings['contact_email'] ?? 'contact@middukheraproduction.in' }}</a>
                                <span class="text-[11px] text-zinc-500 font-light">Guaranteed response within 4 hours</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 shrink-0">
                                <i data-lucide="map-pin" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="text-[10px] uppercase tracking-widest text-zinc-400 font-semibold">Studio Atelier</h4>
                                <p class="text-xs font-semibold text-white mt-0.5 leading-relaxed">{{ $siteSettings['contact_address'] ?? 'Middukhera Production Studio, India' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Google Maps Card / Location -->
                <div class="site-card rounded-3xl overflow-hidden border border-white/10 p-2 shadow-2xl">
                    <iframe src="https://maps.google.com/maps?q={{ urlencode($siteSettings['contact_address'] ?? 'Mumbai') }}&t=&z=13&ie=UTF8&iwloc=&output=embed" width="100%" height="220" style="border:0; border-radius: 1.25rem;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>

            <!-- Right: Contact Form -->
            <div class="lg:col-span-7 site-card rounded-3xl border border-white/10 p-8 sm:p-10 space-y-6 shadow-2xl reveal reveal-right">
                <div>
                    <h3 class="font-serif text-2xl font-bold text-white">Send Studio Message</h3>
                    <p class="text-xs text-zinc-400 mt-1">Provide your project requirements for an immediate bespoke consultation quote.</p>
                </div>

                <form method="POST" action="{{ route('contact.submit') }}" class="space-y-5">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-zinc-300">Your Full Name</label>
                            <input type="text" name="name" required value="{{ old('name') }}" placeholder="e.g. Siddharth Roy" class="w-full px-4 py-2.5 rounded-xl bg-black/40 border border-white/10 text-white text-xs focus:border-theme-primary">
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-zinc-300">Email Address</label>
                            <input type="email" name="email" required value="{{ old('email') }}" placeholder="siddharth@example.com" class="w-full px-4 py-2.5 rounded-xl bg-black/40 border border-white/10 text-white text-xs focus:border-theme-primary">
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-zinc-300">Subject / Category</label>
                        <input type="text" name="subject" required value="{{ old('subject') }}" placeholder="e.g. Wedding Cinematography Booking / Editorial Campaign Inquiry" class="w-full px-4 py-2.5 rounded-xl bg-black/40 border border-white/10 text-white text-xs focus:border-theme-primary">
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-semibold text-zinc-300">Message & Shoot Concept</label>
                        <textarea name="message" rows="5" required placeholder="Describe your vision, target shoot dates, wardrobe ideas, and any questions you have for our team..." class="w-full px-4 py-2.5 rounded-xl bg-black/40 border border-white/10 text-white text-xs leading-relaxed focus:border-theme-primary">{{ old('message') }}</textarea>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full py-4 rounded-full font-bold text-xs uppercase tracking-wider btn-gold-dynamic shadow-xl shadow-[var(--theme-primary)]/20 flex items-center justify-center gap-2 hover:scale-[1.01] transition">
                            <i data-lucide="send" class="w-4 h-4"></i>
                            <span>Send Commission Inquiry</span>
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection
