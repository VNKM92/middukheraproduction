@extends('layouts.app')

@section('content')
<div class="relative overflow-hidden py-16 px-4 sm:px-6 lg:px-8">
  <!-- Glow Orbs -->
  <div class="hero-glow top-10 left-10"></div>
  <div class="hero-glow-gold bottom-10 right-10"></div>

  <div class="max-w-7xl mx-auto relative z-10 space-y-24">
    <!-- Header Hero -->
    <div class="text-center max-w-3xl mx-auto space-y-6 reveal">
      <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full border border-[#e5c158]/30 bg-[#e5c158]/5 text-xs text-[#e5c158] font-semibold tracking-wider uppercase">
        <i data-lucide="info" class="w-3.5 h-3.5"></i>
        <span>Our Chronicles</span>
      </div>
      <h1 class="font-serif text-4xl sm:text-5xl lg:text-6xl font-bold text-white">
        Behind The <span class="text-gold-gradient">Middukhera Lens</span>
      </h1>
      <div class="w-20 h-0.5 bg-gradient-to-r from-transparent via-[#e5c158] to-transparent mx-auto mt-4 mb-6"></div>
      <p class="text-lg text-gray-400 font-light leading-relaxed">
        We are visual preservationists, dedicated to capturing couture designs, raw human expressions, and architectural masterpieces with unparalleled fidelity.
      </p>
    </div>

    <!-- Layout Split Philosophy -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
      <div class="lg:col-span-5 relative reveal">
        <div class="aspect-[4/5] rounded-[2rem] overflow-hidden border border-white/10 p-2 bg-[#111019]/60 backdrop-blur-md">
          <div class="w-full h-full rounded-[1.7rem] overflow-hidden hover-zoom-img-parent">
            <img src="https://images.unsplash.com/photo-1542038784456-1ea8e935640e?q=80&w=800&auto=format&fit=crop" 
                 alt="Camera Lens Details" 
                 class="w-full h-full object-cover hover-zoom-img">
          </div>
        </div>
      </div>

      <div class="lg:col-span-7 space-y-8 reveal reveal-right">
        <h2 class="font-serif text-3xl font-bold text-white">Our Creative Manifesto</h2>
        <p class="text-gray-400 font-light leading-relaxed">
          Founded in 2018, Middukhera Studio emerged from a singular conviction: photography is not simply the recording of a scene; it is the curation of light and emotion to construct a legacy.
        </p>

        <!-- Pillars -->
        <div class="space-y-6">
          <div class="flex items-start space-x-4">
            <div class="bg-[#e5c158]/10 border border-[#e5c158]/20 p-3 rounded-2xl text-[#e5c158] shrink-0">
              <i data-lucide="eye" class="w-6 h-6"></i>
            </div>
            <div>
              <h3 class="text-lg font-bold text-white">Avante-Garde Vision</h3>
              <p class="text-sm text-gray-400 font-light leading-relaxed mt-1">We reject the generic. Every shoot begins with detailed moodboards, artistic concept briefs, and bespoke lighting coordinates.</p>
            </div>
          </div>

          <div class="flex items-start space-x-4">
            <div class="bg-[#8b5cf6]/10 border border-[#8b5cf6]/20 p-3 rounded-2xl text-[#8b5cf6] shrink-0">
              <i data-lucide="award" class="w-6 h-6"></i>
            </div>
            <div>
              <h3 class="text-lg font-bold text-white">Impeccable Precision</h3>
              <p class="text-sm text-gray-400 font-light leading-relaxed mt-1">Utilizing premium medium-format digital sensors, master-class lighting umbrellas, and expert retouchers to ensure flawless physical prints.</p>
            </div>
          </div>

          <div class="flex items-start space-x-4">
            <div class="bg-[#e5c158]/10 border border-[#e5c158]/20 p-3 rounded-2xl text-[#e5c158] shrink-0">
              <i data-lucide="clock" class="w-6 h-6"></i>
            </div>
            <div>
              <h3 class="text-lg font-bold text-white">Timeless Preservation</h3>
              <p class="text-sm text-gray-400 font-light leading-relaxed mt-1">We frame archives. Our signature albums are bound in handmade full-grain Italian leather designed to withstand generations.</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Timeline of Journey -->
    <div class="space-y-12">
      <div class="text-center max-w-2xl mx-auto reveal">
        <h2 class="font-serif text-3xl font-bold text-white">Timeline of Milestones</h2>
        <p class="text-gray-400 text-sm font-light mt-2">A quick retrospective of how we rose to global recognition.</p>
      </div>

      <div class="relative border-l border-white/10 max-w-3xl mx-auto pl-8 space-y-12 reveal">
        <!-- Milestone 1 -->
        <div class="relative">
          <div class="absolute -left-[41px] top-1 bg-gradient-to-r from-gold-light to-gold-dark w-5 h-5 rounded-full border-4 border-[#07060a]"></div>
          <div class="text-xs uppercase tracking-widest text-[#e5c158] font-bold">2018 • The Spark</div>
          <h4 class="text-lg font-bold text-white mt-1">Inception of Middukhera</h4>
          <p class="text-sm text-gray-400 font-light mt-1">Opened our boutique physical studio in Mumbai, focusing purely on high-contrast portraiture and fine art black-and-white printings.</p>
        </div>

        <!-- Milestone 2 -->
        <div class="relative">
          <div class="absolute -left-[41px] top-1 bg-gradient-to-r from-gold-light to-gold-dark w-5 h-5 rounded-full border-4 border-[#07060a]"></div>
          <div class="text-xs uppercase tracking-widest text-[#e5c158] font-bold">2020 • Going Editorial</div>
          <h4 class="text-lg font-bold text-white mt-1">First National Fashion Feature</h4>
          <p class="text-sm text-gray-400 font-light mt-1">Commissioned to shoot the summer collection of two premium national design houses, getting featured in mainstream design journals.</p>
        </div>

        <!-- Milestone 3 -->
        <div class="relative">
          <div class="absolute -left-[41px] top-1 bg-gradient-to-r from-gold-light to-gold-dark w-5 h-5 rounded-full border-4 border-[#07060a]"></div>
          <div class="text-xs uppercase tracking-widest text-[#e5c158] font-bold">2023 • Elite Standard</div>
          <h4 class="text-lg font-bold text-white mt-1">International Expansion & Tech Upgrades</h4>
          <p class="text-sm text-gray-400 font-light mt-1">Upgraded our main systems to Hasselblad medium format equipment and expanded services to cover luxury destination weddings globally.</p>
        </div>
      </div>
    </div>

    <!-- Team Grid -->
    <div class="space-y-16">
      <div class="text-center max-w-2xl mx-auto reveal">
        <h2 class="font-serif text-3xl font-bold text-white">Our Creative Directors</h2>
        <div class="w-16 h-0.5 bg-gradient-to-r from-transparent via-[#e5c158] to-transparent mx-auto mt-2"></div>
        <p class="text-gray-400 text-sm font-light mt-4">The visual visionaries shaping light, fashion sets, and print dynamics.</p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
        <!-- Member 1 -->
        <div class="glass-panel rounded-3xl overflow-hidden p-4 text-center group hover:border-[#e5c158]/20 transition-all duration-300 reveal">
          <div class="aspect-square w-full rounded-2xl overflow-hidden mb-4 hover-zoom-img-parent">
            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=400&auto=format&fit=crop" alt="Arjun Mehta" class="w-full h-full object-cover hover-zoom-img">
          </div>
          <h3 class="text-lg font-serif font-bold text-white group-hover:text-[#e5c158] transition-colors">Arjun Mehta</h3>
          <span class="text-xs uppercase tracking-widest text-[#e5c158] font-medium">Founder & Head Photographer</span>
          <p class="text-xs text-gray-400 mt-2 font-light">12+ years shooting editorial campaigns. Has a sharp eye for contrast geometry.</p>
        </div>

        <!-- Member 2 -->
        <div class="glass-panel rounded-3xl overflow-hidden p-4 text-center group hover:border-[#e5c158]/20 transition-all duration-300 reveal" style="transition-delay: 100ms">
          <div class="aspect-square w-full rounded-2xl overflow-hidden mb-4 hover-zoom-img-parent">
            <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=400&auto=format&fit=crop" alt="Priya Sen" class="w-full h-full object-cover hover-zoom-img">
          </div>
          <h3 class="text-lg font-serif font-bold text-white group-hover:text-[#e5c158] transition-colors">Priya Sen</h3>
          <span class="text-xs uppercase tracking-widest text-[#e5c158] font-medium">Senior Wardrobe Stylist</span>
          <p class="text-xs text-gray-400 mt-2 font-light">Ex-magazine stylist. Guides designers and clients on color matching and fabric flow.</p>
        </div>

        <!-- Member 3 -->
        <div class="glass-panel rounded-3xl overflow-hidden p-4 text-center group hover:border-[#e5c158]/20 transition-all duration-300 reveal" style="transition-delay: 200ms">
          <div class="aspect-square w-full rounded-2xl overflow-hidden mb-4 hover-zoom-img-parent">
            <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?q=80&w=400&auto=format&fit=crop" alt="Vikram Roy" class="w-full h-full object-cover hover-zoom-img">
          </div>
          <h3 class="text-lg font-serif font-bold text-white group-hover:text-[#e5c158] transition-colors">Vikram Roy</h3>
          <span class="text-xs uppercase tracking-widest text-[#e5c158] font-medium">Cinematic Wedding Director</span>
          <p class="text-xs text-gray-400 mt-2 font-light">Specializes in slow-shutter aesthetics and capture of authentic raw romance moments.</p>
        </div>

        <!-- Member 4 -->
        <div class="glass-panel rounded-3xl overflow-hidden p-4 text-center group hover:border-[#e5c158]/20 transition-all duration-300 reveal" style="transition-delay: 300ms">
          <div class="aspect-square w-full rounded-2xl overflow-hidden mb-4 hover-zoom-img-parent">
            <img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?q=80&w=400&auto=format&fit=crop" alt="Elena Rostova" class="w-full h-full object-cover hover-zoom-img">
          </div>
          <h3 class="text-lg font-serif font-bold text-white group-hover:text-[#e5c158] transition-colors">Elena Rostova</h3>
          <span class="text-xs uppercase tracking-widest text-[#e5c158] font-medium">Master Retoucher</span>
          <p class="text-xs text-gray-400 mt-2 font-light">Fine arts graduate. Ensures pixel-perfect dynamic range editing and color curves.</p>
        </div>
      </div>
    </div>

    <!-- CTA Section -->
    <div class="glass-panel-gold rounded-3xl p-8 sm:p-12 text-center max-w-4xl mx-auto relative overflow-hidden border border-[#e5c158]/20 reveal reveal-scale">
      <h3 class="font-serif text-2xl sm:text-3xl font-bold text-white">Experience Our Visual Masterclass</h3>
      <p class="text-sm text-gray-400 font-light mt-3 max-w-xl mx-auto">Learn more about our customized studio booking workflow or secure a designer slot now.</p>
      <div class="mt-6 flex flex-wrap justify-center gap-4">
        <a href="{{ route('home') }}#packages" class="px-6 py-3 bg-gradient-to-r from-gold-light via-gold to-gold-dark text-black font-semibold rounded-full hover:scale-105 transition-all">Book Now</a>
        <a href="{{ route('contact') }}" class="px-6 py-3 border border-white/10 hover:border-white/20 bg-white/5 rounded-full text-white font-medium transition-all">Get in Touch</a>
      </div>
    </div>

  </div>
</div>
@endsection
