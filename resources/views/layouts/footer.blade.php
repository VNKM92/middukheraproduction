<footer class="site-card border-t border-white/5 relative z-10 pt-16 pb-12 mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 mb-12">
            <!-- Brand Column -->
            <div class="lg:col-span-2 space-y-4">
                <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                    <span class="text-2xl font-serif tracking-widest font-bold text-white group-hover:text-[var(--theme-primary)] transition-colors">
                        {{ $siteSettings['logo_text'] ?? 'MIDDUKHERA' }}
                    </span>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded tracking-widest bg-theme-primary text-black">
                        {{ $siteSettings['logo_sub'] ?? 'PRODUCTION' }}
                    </span>
                </a>
                <p class="text-sm text-zinc-400 max-w-sm leading-relaxed">
                    {{ $siteSettings['site_tagline'] ?? 'Capturing Eternal Elegance & High-Fashion Artistry. Premium photoshoot packages with bespoke studio craftsmanship.' }}
                </p>
                <div class="flex items-center gap-3 pt-2">
                    @if(!empty($siteSettings['social_instagram']))
                        <a href="{{ $siteSettings['social_instagram'] }}" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-zinc-400 hover:text-white hover:border-[var(--theme-primary)] hover:bg-[var(--theme-primary)]/10 transition-all duration-300">
                            <i data-lucide="instagram" class="w-4 h-4"></i>
                        </a>
                    @endif
                    @if(!empty($siteSettings['social_facebook']))
                        <a href="{{ $siteSettings['social_facebook'] }}" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-zinc-400 hover:text-white hover:border-[var(--theme-primary)] hover:bg-[var(--theme-primary)]/10 transition-all duration-300">
                            <i data-lucide="facebook" class="w-4 h-4"></i>
                        </a>
                    @endif
                    @if(!empty($siteSettings['social_youtube']))
                        <a href="{{ $siteSettings['social_youtube'] }}" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-zinc-400 hover:text-white hover:border-[var(--theme-primary)] hover:bg-[var(--theme-primary)]/10 transition-all duration-300">
                            <i data-lucide="youtube" class="w-4 h-4"></i>
                        </a>
                    @endif
                    @if(!empty($siteSettings['social_whatsapp']))
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $siteSettings['social_whatsapp']) }}" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-zinc-400 hover:text-white hover:border-emerald-500 hover:bg-emerald-500/10 transition-all duration-300">
                            <i data-lucide="message-circle" class="w-4 h-4"></i>
                        </a>
                    @endif
                </div>

                <div class="pt-2 flex items-center gap-2 text-xs text-zinc-400">
                    <i data-lucide="shield-check" class="w-4 h-4 text-emerald-400 shrink-0"></i>
                    <span>Razorpay Verified Merchant &bull; 256-bit SSL</span>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="space-y-3">
                <h4 class="text-sm font-semibold text-white uppercase tracking-wider">Explore</h4>
                <ul class="space-y-2 text-sm text-zinc-400">
                    <li><a href="{{ route('home') }}" class="hover:text-[var(--theme-primary)] transition-colors">Home</a></li>
                    <li><a href="{{ route('about') }}" class="hover:text-[var(--theme-primary)] transition-colors">About Studio</a></li>
                    <li><a href="{{ route('gallery') }}" class="hover:text-[var(--theme-primary)] transition-colors">Master Gallery</a></li>
                    <li><a href="{{ route('blog') }}" class="hover:text-[var(--theme-primary)] transition-colors">Journal & Articles</a></li>
                    <li><a href="{{ route('contact') }}" class="hover:text-[var(--theme-primary)] transition-colors">Contact Concierge</a></li>
                </ul>
            </div>

            <!-- Legal & Compliance Policies (Razorpay Mandatory) -->
            <div class="space-y-3">
                <h4 class="text-sm font-semibold text-white uppercase tracking-wider">Policies & Legal</h4>
                <ul class="space-y-2 text-sm text-zinc-400">
                    <li><a href="{{ route('terms') }}" class="hover:text-[var(--theme-primary)] transition-colors">Terms of Service</a></li>
                    <li><a href="{{ route('privacy') }}" class="hover:text-[var(--theme-primary)] transition-colors">Privacy Policy</a></li>
                    <li><a href="{{ route('refund-policy') }}" class="hover:text-[var(--theme-primary)] transition-colors">Refund & Cancellation</a></li>
                    <li><a href="{{ route('shipping-policy') }}" class="hover:text-[var(--theme-primary)] transition-colors">Shipping & Delivery</a></li>
                    <li><a href="{{ route('disclaimer') }}" class="hover:text-[var(--theme-primary)] transition-colors">Disclaimer</a></li>
                </ul>
            </div>

            <!-- Contact & Hours -->
            <div class="space-y-3">
                <h4 class="text-sm font-semibold text-white uppercase tracking-wider">Studio Concierge</h4>
                <div class="space-y-2 text-xs text-zinc-400">
                    <p class="flex items-start gap-2">
                        <i data-lucide="map-pin" class="w-4 h-4 text-theme-primary shrink-0 mt-0.5"></i>
                        <span>{{ $siteSettings['contact_address'] ?? 'Middukhera Production Studio, India' }}</span>
                    </p>
                    <p class="flex items-center gap-2">
                        <i data-lucide="phone" class="w-4 h-4 text-theme-primary shrink-0"></i>
                        <a href="tel:{{ $siteSettings['contact_phone'] ?? '+919876543210' }}" class="hover:text-white">{{ $siteSettings['contact_phone'] ?? '+91 98765 43210' }}</a>
                    </p>
                    <p class="flex items-center gap-2">
                        <i data-lucide="mail" class="w-4 h-4 text-theme-primary shrink-0"></i>
                        <a href="mailto:{{ $siteSettings['contact_email'] ?? 'contact@middukheraproduction.in' }}" class="hover:text-white">{{ $siteSettings['contact_email'] ?? 'contact@middukheraproduction.in' }}</a>
                    </p>
                    <p class="flex items-center gap-2">
                        <i data-lucide="clock" class="w-4 h-4 text-theme-primary shrink-0"></i>
                        <span>{{ $siteSettings['operating_hours'] ?? 'Mon - Sun: 09:00 AM - 09:00 PM IST' }}</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Bottom Copyright & Legal -->
        <div class="pt-8 border-t border-white/5 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-zinc-500">
            <p>&copy; {{ date('Y') }} {{ $siteSettings['site_name'] ?? 'Middukhera Production' }}. All rights reserved.</p>
            <div class="flex flex-wrap items-center gap-4 sm:gap-6">
                <a href="{{ route('sitemap.xml') }}" target="_blank" class="hover:text-zinc-300 transition-colors">Sitemap</a>
                <a href="{{ route('terms') }}" class="hover:text-zinc-300 transition-colors">Terms</a>
                <a href="{{ route('privacy') }}" class="hover:text-zinc-300 transition-colors">Privacy</a>
                <a href="{{ route('refund-policy') }}" class="hover:text-zinc-300 transition-colors">Refunds</a>
                <a href="{{ route('shipping-policy') }}" class="hover:text-zinc-300 transition-colors">Shipping</a>
                @guest
                    <a href="{{ route('vendor.register.show') }}" class="text-theme-primary hover:underline font-medium">Join as Photographer</a>
                @endguest
            </div>
        </div>
    </div>
</footer>
