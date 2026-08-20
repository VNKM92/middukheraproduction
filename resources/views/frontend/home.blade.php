@extends('layouts.app')

@section('content')
<div class="relative overflow-hidden">
    <!-- Ambient Background Glows -->
    <div class="hero-glow top-20 left-10 opacity-60"></div>
    <div class="hero-glow-gold top-[700px] right-10 opacity-40"></div>
    <div class="hero-glow bottom-40 left-1/3 opacity-50"></div>

    <!-- 1. HERO SECTION (Dynamic from Super Admin Settings) -->
    <section class="relative min-h-[92vh] flex items-center justify-center pt-8 pb-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto w-full grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Left Text Content -->
            <div class="lg:col-span-7 space-y-8 text-center lg:text-left z-10 reveal">
                <!-- Pill Badge -->
                <div class="inline-flex items-center space-x-2 px-4 py-1.5 rounded-full border border-white/10 bg-white/5 backdrop-blur-md text-xs text-theme-primary font-semibold tracking-wider uppercase shadow-inner">
                    <i data-lucide="sparkles" class="w-3.5 h-3.5 text-theme-primary"></i>
                    <span>{{ $siteSettings['hero_badge'] ?? '✨ INDIA’S PREMIER LUXURY PRODUCTION HOUSE' }}</span>
                </div>
                
                <!-- Main H1 Headline -->
                <h1 class="font-serif text-4xl sm:text-6xl xl:text-7xl font-bold tracking-tight text-white leading-[1.15]">
                    {{ $siteSettings['hero_title'] ?? 'Transforming Ephemeral Moments Into Timeless High-Art Masterpieces' }}
                </h1>
                
                <!-- Subtitle -->
                <p class="text-base sm:text-lg text-zinc-300 max-w-2xl mx-auto lg:mx-0 leading-relaxed font-light">
                    {{ $siteSettings['hero_subtitle'] ?? 'Bespoke couture portraiture, celebrity fashion editorials, and cinematic wedding archives captured with world-class medium-format clarity.' }}
                </p>

                <!-- Hero Action CTAs -->
                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                    <a href="{{ $siteSettings['hero_cta_link'] ?? '#packages' }}" class="w-full sm:w-auto text-center px-8 py-4 rounded-full font-bold text-sm uppercase tracking-wider btn-gold-dynamic shadow-2xl shadow-[var(--theme-primary)]/25 hover:scale-105 transition-all duration-300 flex items-center justify-center gap-2">
                        <span>{{ $siteSettings['hero_cta_text'] ?? 'Explore Packages & Book' }}</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                    <a href="{{ route('gallery') }}" class="w-full sm:w-auto text-center px-8 py-4 rounded-full text-sm font-semibold text-white border border-white/15 hover:border-white/40 bg-white/5 hover:bg-white/10 backdrop-blur-md transition-all duration-300 flex items-center justify-center gap-2">
                        <i data-lucide="camera" class="w-4 h-4 text-theme-primary"></i>
                        <span>{{ $siteSettings['hero_secondary_cta'] ?? 'View Master Portfolio' }}</span>
                    </a>
                </div>

                <!-- Trust Micro-Highlights -->
                <div class="pt-4 flex items-center justify-center lg:justify-start gap-6 text-xs text-zinc-400">
                    <span class="flex items-center gap-1.5">
                        <i data-lucide="shield-check" class="w-4 h-4 text-emerald-400"></i>
                        <span>Razorpay Verified Gateway</span>
                    </span>
                    <span class="flex items-center gap-1.5">
                        <i data-lucide="award" class="w-4 h-4 text-theme-primary"></i>
                        <span>Medium Format 100MP Sensor</span>
                    </span>
                </div>
            </div>

            <!-- Right Hero Visual Showcase -->
            <div class="lg:col-span-5 relative z-10 reveal reveal-right">
                <div class="relative w-full max-w-md mx-auto aspect-[3/4] rounded-[2.5rem] overflow-hidden border border-white/15 p-2.5 site-card shadow-2xl">
                    <div class="w-full h-full rounded-[2.2rem] overflow-hidden hover-zoom-img-parent relative">
                        <img src="{{ $siteSettings['hero_bg_image'] ?? 'https://images.unsplash.com/photo-1542038784456-1ea8e935640e?q=80&w=1200&auto=format&fit=crop' }}" 
                             alt="Luxury Studio Production" 
                             class="w-full h-full object-cover hover-zoom-img">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
                    </div>

                    <!-- Floating Badge: Master Artistry -->
                    <div class="absolute -bottom-4 -left-4 site-card p-4 rounded-2xl flex items-center space-x-3 shadow-2xl border border-white/15 backdrop-blur-xl">
                        <div class="w-10 h-10 rounded-xl bg-theme-primary flex items-center justify-center text-black font-bold">
                            <i data-lucide="aperture" class="w-5 h-5 animate-spin" style="animation-duration: 15s"></i>
                        </div>
                        <div>
                            <div class="text-[10px] uppercase tracking-wider text-zinc-400 font-semibold">Studio Quality</div>
                            <div class="text-xs font-bold text-white">100MP Phase One Raw</div>
                        </div>
                    </div>

                    <!-- Floating Badge: Rating -->
                    <div class="absolute -top-3 -right-3 site-card py-2 px-3.5 rounded-full flex items-center space-x-2 shadow-2xl border border-white/15 backdrop-blur-xl">
                        <i data-lucide="star" class="w-3.5 h-3.5 fill-amber-400 text-amber-400"></i>
                        <span class="text-xs font-bold text-white">{{ $siteSettings['stat_rating'] ?? '4.98' }} Rating</span>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- 2. DYNAMIC LIVE METRICS & STATS BAR -->
    <section class="py-10 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="site-card rounded-3xl p-8 sm:p-10 border border-white/10 shadow-2xl reveal reveal-scale">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center divide-y md:divide-y-0 md:divide-x divide-white/10">
                    <div class="pt-4 md:pt-0">
                        <div class="text-3xl sm:text-5xl font-serif font-bold text-theme-primary">{{ $siteSettings['stat_experience'] ?? '12+ Years' }}</div>
                        <div class="text-xs uppercase tracking-widest text-zinc-400 mt-2 font-semibold">{{ $siteSettings['stat_experience_label'] ?? 'Creative Mastery' }}</div>
                    </div>
                    <div class="pt-4 md:pt-0">
                        <div class="text-3xl sm:text-5xl font-serif font-bold text-theme-primary">{{ $siteSettings['stat_shoots'] ?? '2,800+' }}</div>
                        <div class="text-xs uppercase tracking-widest text-zinc-400 mt-2 font-semibold">{{ $siteSettings['stat_shoots_label'] ?? 'Sessions Captured' }}</div>
                    </div>
                    <div class="pt-4 md:pt-0">
                        <div class="text-3xl sm:text-5xl font-serif font-bold text-theme-primary">{{ $siteSettings['stat_awards'] ?? '42+' }}</div>
                        <div class="text-xs uppercase tracking-widest text-zinc-400 mt-2 font-semibold">{{ $siteSettings['stat_awards_label'] ?? 'Global Accolades' }}</div>
                    </div>
                    <div class="pt-4 md:pt-0">
                        <div class="text-3xl sm:text-5xl font-serif font-bold text-theme-primary">{{ $siteSettings['stat_rating'] ?? '4.98' }} / 5.0</div>
                        <div class="text-xs uppercase tracking-widest text-zinc-400 mt-2 font-semibold">{{ $siteSettings['stat_rating_label'] ?? 'Client Delight Score' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. STUDIO SPECIALTIES REEL -->
    <section class="py-20 border-y border-white/5 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 reveal">
                <span class="text-xs font-bold uppercase tracking-widest text-theme-primary">Bespoke Pillars</span>
                <h2 class="font-serif text-3xl sm:text-4xl font-bold text-white mt-2">The Middukhera Signature Disciplines</h2>
                <div class="w-16 h-0.5 bg-theme-primary mx-auto mt-4 mb-4"></div>
                <p class="text-zinc-400 text-sm leading-relaxed">Explore our specialized visual suites, each crafted with specialized lighting setups, luxury wardrobe styling, and master colorists.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Specialty 1 -->
                <div class="site-card hover-zoom-img-parent rounded-3xl overflow-hidden p-5 border border-white/10 group hover:border-[var(--theme-primary)] transition-all duration-500 reveal">
                    <div class="aspect-[4/3] rounded-2xl overflow-hidden mb-6 relative">
                        <img src="https://images.unsplash.com/photo-1519741497674-611481863552?q=80&w=800&auto=format&fit=crop" alt="Cinematic Weddings" class="w-full h-full object-cover hover-zoom-img">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    </div>
                    <h3 class="text-xl font-serif font-bold text-white group-hover:text-theme-primary transition-colors mb-2">Cinematic Weddings</h3>
                    <p class="text-zinc-400 text-xs leading-relaxed mb-4">Documenting raw emotional vows, intricate embroidery, and heirloom love stories across royal palaces and exotic destinations.</p>
                    <a href="#packages" class="inline-flex items-center text-theme-primary font-semibold text-xs group-hover:translate-x-1.5 transition-transform">
                        <span>View Wedding Packages</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1"></i>
                    </a>
                </div>

                <!-- Specialty 2 -->
                <div class="site-card hover-zoom-img-parent rounded-3xl overflow-hidden p-5 border border-white/10 group hover:border-[var(--theme-primary)] transition-all duration-500 reveal" style="transition-delay: 150ms">
                    <div class="aspect-[4/3] rounded-2xl overflow-hidden mb-6 relative">
                        <img src="https://images.unsplash.com/photo-1509631179647-0177331693ae?q=80&w=800&auto=format&fit=crop" alt="High Fashion" class="w-full h-full object-cover hover-zoom-img">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    </div>
                    <h3 class="text-xl font-serif font-bold text-white group-hover:text-theme-primary transition-colors mb-2">Haute Couture Fashion</h3>
                    <p class="text-zinc-400 text-xs leading-relaxed mb-4">High-impact editorials, designer lookbooks, and runway modeling campaigns with avante-garde lighting direction.</p>
                    <a href="#packages" class="inline-flex items-center text-theme-primary font-semibold text-xs group-hover:translate-x-1.5 transition-transform">
                        <span>View Fashion Packages</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1"></i>
                    </a>
                </div>

                <!-- Specialty 3 -->
                <div class="site-card hover-zoom-img-parent rounded-3xl overflow-hidden p-5 border border-white/10 group hover:border-[var(--theme-primary)] transition-all duration-500 reveal" style="transition-delay: 300ms">
                    <div class="aspect-[4/3] rounded-2xl overflow-hidden mb-6 relative">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=800&auto=format&fit=crop" alt="Fine Art Portraits" class="w-full h-full object-cover hover-zoom-img">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    </div>
                    <h3 class="text-xl font-serif font-bold text-white group-hover:text-theme-primary transition-colors mb-2">Fine Art Portraiture</h3>
                    <p class="text-zinc-400 text-xs leading-relaxed mb-4">Intimate studio sessions capturing heritage portraits, executive branding, and museum-grade printed legacy pieces.</p>
                    <a href="#packages" class="inline-flex items-center text-theme-primary font-semibold text-xs group-hover:translate-x-1.5 transition-transform">
                        <span>View Portrait Packages</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5 ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. DYNAMIC PRICING PACKAGES SECTION -->
    <section id="packages" class="py-24 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 reveal">
                <span class="text-xs font-bold uppercase tracking-widest text-theme-primary">Transparent Studio Pricing</span>
                <h2 class="font-serif text-3xl sm:text-4xl font-bold text-white mt-2">Curated Signature Packages</h2>
                <div class="w-16 h-0.5 bg-theme-primary mx-auto mt-4 mb-4"></div>
                <p class="text-zinc-400 text-sm leading-relaxed">Select your desired tier and reserve seamlessly with instant Razorpay booking confirmation.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($packages as $idx => $pkg)
                    @php
                        $features = is_array($pkg->features) ? $pkg->features : json_decode($pkg->features, true) ?? explode(',', $pkg->features);
                        $isFeatured = $idx === 2 || str_contains(strtolower($pkg->name), 'wedding');
                    @endphp
                    <div class="site-card rounded-3xl overflow-hidden border {{ $isFeatured ? 'border-[var(--theme-primary)] ring-1 ring-[var(--theme-primary)]/40 shadow-2xl' : 'border-white/10' }} flex flex-col justify-between p-8 hover:border-[var(--theme-primary)] transition-all duration-300 reveal group" style="transition-delay: {{ $idx * 80 }}ms">
                        
                        <div class="space-y-6">
                            <!-- Package Image -->
                            <div class="aspect-video w-full rounded-2xl overflow-hidden hover-zoom-img-parent border border-white/5 relative">
                                <img src="{{ $pkg->image_path ?: 'https://images.unsplash.com/photo-1492691527719-9d1e07e534b4?w=800' }}" alt="{{ $pkg->name }}" class="w-full h-full object-cover hover-zoom-img">
                                @if($isFeatured)
                                    <span class="absolute top-3 right-3 text-[10px] font-bold uppercase tracking-widest px-2.5 py-1 rounded-full bg-theme-primary text-black">
                                        Most Popular
                                    </span>
                                @endif
                            </div>

                            <!-- Package Info -->
                            <div>
                                <h3 class="text-2xl font-serif font-bold text-white group-hover:text-theme-primary transition-colors">{{ $pkg->name }}</h3>
                                <p class="text-xs text-zinc-400 mt-2 leading-relaxed line-clamp-3">{!! strip_tags($pkg->description) !!}</p>
                            </div>

                            <!-- Price Range -->
                            <div class="pt-4 border-t border-white/5">
                                <div class="text-[10px] uppercase tracking-widest text-zinc-400 font-semibold">Tier Investment</div>
                                <div class="text-2xl sm:text-3xl font-serif font-bold text-white mt-1">
                                    {{ $siteSettings['currency_symbol'] ?? '₹' }}{{ number_format($pkg->price_min) }}
                                    <span class="text-xs font-sans font-normal text-zinc-400">to {{ $siteSettings['currency_symbol'] ?? '₹' }}{{ number_format($pkg->price_max) }}</span>
                                </div>
                            </div>

                            <!-- Included Features -->
                            <div class="space-y-2.5 pt-2">
                                <div class="text-[11px] font-bold uppercase tracking-wider text-zinc-300">Package Deliverables:</div>
                                @if(!empty($features))
                                    @foreach($features as $feat)
                                        <div class="flex items-start text-xs text-zinc-300 gap-2">
                                            <i data-lucide="check-circle" class="w-4 h-4 text-theme-primary shrink-0 mt-0.5"></i>
                                            <span class="leading-relaxed">{{ trim($feat) }}</span>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <!-- CTA Button -->
                        <div class="mt-8 pt-4 border-t border-white/5">
                            <a href="{{ route('booking.checkout', $pkg->slug) }}" class="w-full block text-center py-3.5 rounded-full font-bold text-xs uppercase tracking-wider transition-all duration-300 {{ $isFeatured ? 'btn-gold-dynamic shadow-xl shadow-[var(--theme-primary)]/20' : 'border border-white/15 bg-white/5 text-white hover:bg-white/10 hover:border-white/30' }}">
                                Reserve Session &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 5. DYNAMIC MASTER GALLERY SHOWCASE -->
    <section class="py-20 border-y border-white/5 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 reveal">
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-theme-primary">Curated Portfolio</span>
                    <h2 class="font-serif text-3xl sm:text-4xl font-bold text-white mt-1">Framed Masterpieces</h2>
                    <div class="w-16 h-0.5 bg-theme-primary mt-3 mb-2"></div>
                    <p class="text-zinc-400 text-sm">A preview of recent fine-art commissions, editorial features, and bridal archives.</p>
                </div>
                <a href="{{ route('gallery') }}" class="inline-flex items-center text-xs font-bold uppercase tracking-wider text-theme-primary hover:underline mt-4 md:mt-0 gap-1.5">
                    <span>View All Portfolio Works</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach($gallery->take(6) as $idx => $gItem)
                    <div class="site-card hover-zoom-img-parent rounded-2xl overflow-hidden border border-white/10 group aspect-[3/4] relative reveal" style="transition-delay: {{ $idx * 60 }}ms">
                        <img src="{{ $gItem->image_path }}" alt="{{ $gItem->title }}" class="w-full h-full object-cover hover-zoom-img">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity p-4 flex flex-col justify-end">
                            <span class="text-[10px] font-bold uppercase tracking-widest text-theme-primary">{{ $gItem->category }}</span>
                            <h4 class="text-xs font-bold text-white truncate">{{ $gItem->title }}</h4>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 6. CLIENT TESTIMONIALS (Alpine.js Slider) -->
    <section class="py-24 relative z-10" x-data="{
        active: 0,
        reviews: [
            {
                quote: 'Middukhera Studio exceeded our wildest expectations. The high-fashion lighting direction and medium-format image clarity produced imagery worthy of Vogue. An absolute triumph.',
                name: 'Natasha Oberoi',
                title: 'High-Fashion Designer & Creative Director',
                avatar: 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=200'
            },
            {
                quote: 'We booked the Royal Wedding package. The candid documentation captured emotional tears, jewelry brilliance, and celebratory fireworks in cinematic perfection. We cherish these archives forever.',
                name: 'Aditya & Tara Kapoor',
                title: 'Private Destination Wedding, Udaipur Palace',
                avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=200'
            },
            {
                quote: 'The executive portraits done for our tech leadership team delivered flawless corporate authority while remaining deeply modern and approachable. Turnaround was lightning fast.',
                name: 'Karan Mehra',
                title: 'Managing Partner, Apex Capital Partners',
                avatar: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?q=80&w=200'
            }
        ],
        next() { this.active = (this.active + 1) % this.reviews.length },
        prev() { this.active = (this.active - 1 + this.reviews.length) % this.reviews.length }
    }">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center reveal">
            <span class="text-xs font-bold uppercase tracking-widest text-theme-primary">Client Experiences</span>
            <h2 class="font-serif text-3xl sm:text-4xl font-bold text-white mt-2">What Our Patrons Say</h2>
            <div class="w-16 h-0.5 bg-theme-primary mx-auto mt-4 mb-12"></div>

            <div class="site-card rounded-3xl p-8 sm:p-14 border border-white/10 shadow-2xl relative">
                <!-- Quotation Mark Icon -->
                <i data-lucide="quote" class="w-12 h-12 text-theme-primary opacity-20 mx-auto mb-6"></i>

                <template x-for="(rev, i) in reviews" :key="i">
                    <div x-show="active === i" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" class="space-y-6">
                        <p class="text-lg sm:text-xl font-serif text-zinc-200 leading-relaxed italic max-w-3xl mx-auto" x-text="'&ldquo;' + rev.quote + '&rdquo;'"></p>
                        
                        <div class="flex items-center justify-center gap-4 pt-4">
                            <img :src="rev.avatar" :alt="rev.name" class="w-12 h-12 rounded-full object-cover border-2 border-[var(--theme-primary)]">
                            <div class="text-left">
                                <div class="font-bold text-white text-sm" x-text="rev.name"></div>
                                <div class="text-xs text-zinc-400" x-text="rev.title"></div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Navigation Controls -->
                <div class="flex items-center justify-center gap-4 mt-8 pt-6 border-t border-white/5">
                    <button @click="prev()" class="w-9 h-9 rounded-full bg-white/5 hover:bg-white/10 border border-white/10 text-white flex items-center justify-center transition">
                        <i data-lucide="chevron-left" class="w-4 h-4"></i>
                    </button>
                    <div class="flex gap-2">
                        <template x-for="(rev, i) in reviews" :key="i">
                            <button @click="active = i" :class="active === i ? 'w-6 bg-[var(--theme-primary)]' : 'w-2 bg-white/20'" class="h-2 rounded-full transition-all duration-300"></button>
                        </template>
                    </div>
                    <button @click="next()" class="w-9 h-9 rounded-full bg-white/5 hover:bg-white/10 border border-white/10 text-white flex items-center justify-center transition">
                        <i data-lucide="chevron-right" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- 7. DYNAMIC JOURNAL / BLOG HIGHLIGHTS -->
    <section class="py-20 border-y border-white/5 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 reveal">
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-theme-primary">Studio Journal</span>
                    <h2 class="font-serif text-3xl sm:text-4xl font-bold text-white mt-1">Stories & Photography Masterclasses</h2>
                    <div class="w-16 h-0.5 bg-theme-primary mt-3 mb-2"></div>
                    <p class="text-zinc-400 text-sm">Insider breakdowns on studio lighting, bridal choreography, and editorial staging.</p>
                </div>
                <a href="{{ route('blog') }}" class="inline-flex items-center text-xs font-bold uppercase tracking-wider text-theme-primary hover:underline mt-4 md:mt-0 gap-1.5">
                    <span>View All Articles</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($blogs->take(3) as $post)
                    <div class="site-card rounded-3xl overflow-hidden border border-white/10 hover:border-white/30 transition-all flex flex-col justify-between group reveal">
                        <div>
                            <div class="aspect-[16/10] overflow-hidden hover-zoom-img-parent relative">
                                <img src="{{ $post->image_path ?: 'https://images.unsplash.com/photo-1542038784456-1ea8e935640e?w=800' }}" alt="{{ $post->title }}" class="w-full h-full object-cover hover-zoom-img">
                            </div>
                            <div class="p-6 space-y-3">
                                <div class="text-[10px] uppercase tracking-wider text-zinc-500 font-semibold">{{ $post->created_at->format('M d, Y') }}</div>
                                <h3 class="text-lg font-serif font-bold text-white group-hover:text-theme-primary transition-colors line-clamp-2">{{ $post->title }}</h3>
                                <p class="text-xs text-zinc-400 leading-relaxed line-clamp-3">{{ $post->excerpt }}</p>
                            </div>
                        </div>
                        <div class="p-6 pt-0">
                            <a href="{{ route('blog.single', $post->slug) }}" class="inline-flex items-center text-xs font-semibold text-theme-primary hover:underline gap-1">
                                <span>Read Full Editorial</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 8. FREQUENTLY ASKED QUESTIONS ACCORDION -->
    <section class="py-20 relative z-10" x-data="{ openFaq: null }">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 reveal">
            <div class="text-center mb-12">
                <span class="text-xs font-bold uppercase tracking-widest text-theme-primary">Clear Answers</span>
                <h2 class="font-serif text-3xl sm:text-4xl font-bold text-white mt-2">Frequently Asked Questions</h2>
                <div class="w-16 h-0.5 bg-theme-primary mx-auto mt-4 mb-4"></div>
            </div>

            <div class="space-y-4">
                <!-- FAQ 1 -->
                <div class="site-card rounded-2xl border border-white/10 overflow-hidden">
                    <button @click="openFaq = openFaq === 1 ? null : 1" class="w-full p-5 text-left flex items-center justify-between font-semibold text-white text-sm">
                        <span>How do I reserve a photoshoot session?</span>
                        <i data-lucide="chevron-down" :class="openFaq === 1 ? 'rotate-180' : ''" class="w-4 h-4 text-theme-primary transition-transform"></i>
                    </button>
                    <div x-show="openFaq === 1" x-collapse class="px-5 pb-5 text-xs text-zinc-400 leading-relaxed border-t border-white/5 pt-3">
                        Select your preferred package from our signature tiers, click "Reserve Session", choose your desired date, and complete your secure deposit via Razorpay. Our concierge will contact you within 24 hours to schedule your creative briefing.
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="site-card rounded-2xl border border-white/10 overflow-hidden">
                    <button @click="openFaq = openFaq === 2 ? null : 2" class="w-full p-5 text-left flex items-center justify-between font-semibold text-white text-sm">
                        <span>What is your typical delivery turnaround for edited photos?</span>
                        <i data-lucide="chevron-down" :class="openFaq === 2 ? 'rotate-180' : ''" class="w-4 h-4 text-theme-primary transition-transform"></i>
                    </button>
                    <div x-show="openFaq === 2" x-collapse class="px-5 pb-5 text-xs text-zinc-400 leading-relaxed border-t border-white/5 pt-3">
                        Initial preview contact sheets are delivered within 48 to 72 hours. Fully color-graded, high-fidelity retouched master plates are completed within 7 to 14 business days, accessible directly from your personal Client Dashboard.
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="site-card rounded-2xl border border-white/10 overflow-hidden">
                    <button @click="openFaq = openFaq === 3 ? null : 3" class="w-full p-5 text-left flex items-center justify-between font-semibold text-white text-sm">
                        <span>Can I reschedule my session if needed?</span>
                        <i data-lucide="chevron-down" :class="openFaq === 3 ? 'rotate-180' : ''" class="w-4 h-4 text-theme-primary transition-transform"></i>
                    </button>
                    <div x-show="openFaq === 3" x-collapse class="px-5 pb-5 text-xs text-zinc-400 leading-relaxed border-t border-white/5 pt-3">
                        Yes, sessions may be rescheduled up to 5 days prior to your booking date without penalty. Please notify our concierge via phone or WhatsApp to select a new convenient studio slot.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 9. FINAL HIGH-CONVERTING CTA BANNER -->
    <section class="py-20 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="site-card rounded-3xl p-10 sm:p-16 border border-white/15 text-center relative overflow-hidden shadow-2xl reveal reveal-scale">
                <div class="max-w-2xl mx-auto space-y-6 relative z-10">
                    <h2 class="font-serif text-3xl sm:text-5xl font-bold text-white">Ready to Create Your Visual Legacy?</h2>
                    <p class="text-sm text-zinc-300 leading-relaxed">
                        Reserve your studio session today or connect directly with our creative director to discuss bespoke production staging.
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-2">
                        <a href="#packages" class="w-full sm:w-auto px-8 py-4 rounded-full font-bold text-xs uppercase tracking-wider btn-gold-dynamic shadow-xl shadow-[var(--theme-primary)]/20">
                            Book Session Now
                        </a>
                        <a href="{{ route('contact') }}" class="w-full sm:w-auto px-8 py-4 rounded-full text-xs font-semibold text-white border border-white/20 hover:bg-white/10 transition">
                            Talk with Concierge
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>
@endsection
