@extends('layouts.app')

@section('content')
<div class="relative overflow-hidden py-16 px-4 sm:px-6 lg:px-8">
  <!-- Glow Orbs -->
  <div class="hero-glow top-10 left-10 opacity-60"></div>
  <div class="hero-glow-gold bottom-10 right-10 opacity-40"></div>

  <div class="max-w-7xl mx-auto relative z-10 space-y-24">
    <!-- Header Hero -->
    <div class="text-center max-w-3xl mx-auto space-y-6 reveal">
      <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full border border-[var(--theme-primary)]/30 bg-[var(--theme-primary)]/5 text-xs text-theme-primary font-semibold tracking-wider uppercase">
        <i data-lucide="info" class="w-3.5 h-3.5"></i>
        <span>{{ $page->subtitle ?? 'Our Chronicles & Creative Philosophy' }}</span>
      </div>
      <h1 class="font-serif text-4xl sm:text-5xl lg:text-6xl font-bold text-white">
        {{ $page->title ?? 'Behind The MK Production Lens' }}
      </h1>
      <div class="w-20 h-0.5 bg-gradient-to-r from-transparent via-[var(--theme-primary)] to-transparent mx-auto mt-4 mb-6"></div>
      <div class="text-base sm:text-lg text-gray-300 font-light leading-relaxed">
        {!! $page->content ?? '<p>We are visual preservationists, dedicated to capturing couture designs, raw human expressions, and architectural masterpieces with unparalleled fidelity.</p>' !!}
      </div>
    </div>

    <!-- Layout Split Philosophy -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
      <div class="lg:col-span-5 relative reveal">
        <div class="aspect-[4/5] rounded-[2rem] overflow-hidden border border-white/10 p-2 bg-[#111019]/60 backdrop-blur-md site-card shadow-2xl">
          <div class="w-full h-full rounded-[1.7rem] overflow-hidden hover-zoom-img-parent">
            <img src="{{ $page->banner_image ?? ($siteSettings['about_banner_image'] ?? 'https://images.unsplash.com/photo-1542038784456-1ea8e935640e?q=80&w=800&auto=format&fit=crop') }}" 
                 alt="Camera Lens Details" 
                 class="w-full h-full object-cover hover-zoom-img">
          </div>
        </div>
      </div>

      <div class="lg:col-span-7 space-y-8 reveal reveal-right">
        <h2 class="font-serif text-3xl font-bold text-white">
          {{ $page->sections['manifesto_title'] ?? 'Our Creative Manifesto' }}
        </h2>
        <p class="text-gray-400 font-light leading-relaxed">
          {{ $page->sections['manifesto_desc'] ?? 'Founded in 2018, our production house emerged from a singular conviction: photography is not simply the recording of a scene; it is the curation of light and emotion to construct a legacy.' }}
        </p>

        <!-- Pillars (Dynamic) -->
        <div class="space-y-6">
          @php
            $pillars = $page->sections['pillars'] ?? [
              ['icon' => 'eye', 'title' => 'Avante-Garde Vision', 'desc' => 'We reject the generic. Every shoot begins with detailed moodboards, artistic concept briefs, and bespoke lighting coordinates.'],
              ['icon' => 'award', 'title' => 'Impeccable Precision', 'desc' => 'Utilizing premium medium-format digital sensors, master-class lighting umbrellas, and expert retouchers to ensure flawless physical prints.'],
              ['icon' => 'clock', 'title' => 'Timeless Preservation', 'desc' => 'We frame archives. Our signature albums are bound in handmade full-grain Italian leather designed to withstand generations.']
            ];
          @endphp

          @foreach($pillars as $pillar)
            <div class="flex items-start space-x-4">
              <div class="bg-[var(--theme-primary)]/10 border border-[var(--theme-primary)]/20 p-3 rounded-2xl text-theme-primary shrink-0">
                <i data-lucide="{{ $pillar['icon'] ?? 'sparkles' }}" class="w-6 h-6"></i>
              </div>
              <div>
                <h3 class="text-lg font-bold text-white">{{ $pillar['title'] }}</h3>
                <p class="text-sm text-gray-400 font-light leading-relaxed mt-1">{{ $pillar['desc'] }}</p>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </div>

    <!-- Timeline of Journey / Milestones (Dynamic) -->
    <div class="space-y-12">
      <div class="text-center max-w-2xl mx-auto reveal">
        <h2 class="font-serif text-3xl font-bold text-white">Timeline of Milestones</h2>
        <p class="text-gray-400 text-sm font-light mt-2">A quick retrospective of how we rose to global recognition.</p>
      </div>

      <div class="relative border-l border-white/10 max-w-3xl mx-auto pl-8 space-y-12 reveal">
        @php
          $milestones = $page->sections['milestones'] ?? [
            ['badge' => '2018 • The Spark', 'title' => 'Inception of Luxury Studio', 'desc' => 'Opened our boutique physical studio focusing purely on high-contrast portraiture and fine art black-and-white printings.'],
            ['badge' => '2020 • Going Editorial', 'title' => 'First National Fashion Feature', 'desc' => 'Commissioned to shoot the summer collection of two premium national design houses, getting featured in mainstream design journals.'],
            ['badge' => '2023 • Elite Standard', 'title' => 'International Expansion & Tech Upgrades', 'desc' => 'Upgraded our main systems to Hasselblad medium format equipment and expanded services to cover luxury destination weddings globally.']
          ];
        @endphp

        @foreach($milestones as $milestone)
          <div class="relative">
            <div class="absolute -left-[41px] top-1 bg-gradient-to-r from-[var(--theme-primary)] to-[var(--theme-secondary)] w-5 h-5 rounded-full border-4 border-[#07060a]"></div>
            <div class="text-xs uppercase tracking-widest text-theme-primary font-bold">{{ $milestone['badge'] ?? $milestone['year'] }}</div>
            <h4 class="text-lg font-bold text-white mt-1">{{ $milestone['title'] }}</h4>
            <p class="text-sm text-gray-400 font-light mt-1">{{ $milestone['desc'] }}</p>
          </div>
        @endforeach
      </div>
    </div>

    <!-- Team Grid (Dynamic from TeamMember Model) -->
    @if(isset($teamMembers) && $teamMembers->count() > 0)
    <div class="space-y-16">
      <div class="text-center max-w-2xl mx-auto reveal">
        <h2 class="font-serif text-3xl font-bold text-white">Our Creative Directors & Crew</h2>
        <div class="w-16 h-0.5 bg-gradient-to-r from-transparent via-[var(--theme-primary)] to-transparent mx-auto mt-2"></div>
        <p class="text-gray-400 text-sm font-light mt-4">The visual visionaries shaping light, fashion sets, and print dynamics.</p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
        @foreach($teamMembers as $member)
          <div class="site-card rounded-3xl overflow-hidden p-4 text-center group border border-white/10 hover:border-[var(--theme-primary)]/30 transition-all duration-300 reveal shadow-xl">
            <div class="aspect-square w-full rounded-2xl overflow-hidden mb-4 hover-zoom-img-parent">
              <img src="{{ $member->image_path ?? 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400' }}" alt="{{ $member->name }}" class="w-full h-full object-cover hover-zoom-img">
            </div>
            <h3 class="text-lg font-serif font-bold text-white group-hover:text-theme-primary transition-colors">{{ $member->name }}</h3>
            <span class="text-xs uppercase tracking-widest text-theme-primary font-medium block mt-0.5">{{ $member->role }}</span>
            @if($member->bio)
              <p class="text-xs text-gray-400 mt-2 font-light line-clamp-3">{{ $member->bio }}</p>
            @endif
            @if($member->instagram_url)
              <div class="mt-3 flex justify-center">
                <a href="{{ $member->instagram_url }}" target="_blank" class="p-1.5 rounded-lg bg-white/5 hover:bg-[var(--theme-primary)]/20 text-zinc-400 hover:text-white transition">
                  <i data-lucide="instagram" class="w-4 h-4"></i>
                </a>
              </div>
            @endif
          </div>
        @endforeach
      </div>
    </div>
    @endif

    <!-- FAQs Section (Dynamic) -->
    @if(isset($faqs) && $faqs->count() > 0)
    <div class="space-y-12 max-w-4xl mx-auto">
      <div class="text-center space-y-3 reveal">
        <span class="text-xs font-bold uppercase tracking-widest text-theme-primary">Got Questions?</span>
        <h2 class="font-serif text-3xl font-bold text-white">Frequently Asked Questions</h2>
        <div class="w-16 h-0.5 bg-theme-primary mx-auto mt-2"></div>
      </div>

      <div class="space-y-4 reveal" x-data="{ openFaq: null }">
        @foreach($faqs as $idx => $faq)
          <div class="site-card rounded-2xl border border-white/10 overflow-hidden transition">
            <button type="button" @click="openFaq = (openFaq === {{ $idx }} ? null : {{ $idx }})" class="w-full p-5 text-left flex items-center justify-between gap-4">
              <span class="text-sm font-semibold text-white">{{ $faq->question }}</span>
              <i data-lucide="chevron-down" class="w-4 h-4 text-theme-primary transition-transform duration-200" :class="openFaq === {{ $idx }} ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="openFaq === {{ $idx }}" x-collapse class="px-5 pb-5 text-xs text-zinc-400 font-light leading-relaxed border-t border-white/5 pt-3">
              {{ $faq->answer }}
            </div>
          </div>
        @endforeach
      </div>
    </div>
    @endif

    <!-- CTA Section -->
    <div class="site-card rounded-3xl p-8 sm:p-12 text-center max-w-4xl mx-auto relative overflow-hidden border border-[var(--theme-primary)]/20 reveal reveal-scale shadow-2xl">
      <h3 class="font-serif text-2xl sm:text-3xl font-bold text-white">Experience Our Visual Masterclass</h3>
      <p class="text-sm text-gray-400 font-light mt-3 max-w-xl mx-auto">Learn more about our customized studio booking workflow or secure a designer slot now.</p>
      <div class="mt-6 flex flex-wrap justify-center gap-4">
        <a href="{{ route('home') }}#packages" class="px-6 py-3 btn-gold-dynamic font-semibold rounded-full hover:scale-105 transition-all text-xs uppercase tracking-wider">Explore Packages</a>
        <a href="{{ route('contact') }}" class="px-6 py-3 border border-white/15 hover:border-white/30 bg-white/5 rounded-full text-white text-xs font-semibold uppercase tracking-wider transition-all">Get in Touch</a>
      </div>
    </div>

  </div>
</div>
@endsection
