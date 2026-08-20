@extends('layouts.app')

@section('content')
<div class="relative overflow-hidden py-16 px-4 sm:px-6 lg:px-8" 
     x-data="{ 
        activeCategory: 'All',
        lightboxOpen: false,
        lightboxImg: '',
        lightboxTitle: '',
        lightboxCat: '',
        openLightbox(img, title, cat) {
            this.lightboxImg = img;
            this.lightboxTitle = title;
            this.lightboxCat = cat;
            this.lightboxOpen = true;
        }
     }">
  <!-- Background glow orbs -->
  <div class="hero-glow top-10 right-10"></div>
  <div class="hero-glow-gold bottom-10 left-10"></div>

  <div class="max-w-7xl mx-auto relative z-10 space-y-12">
    <!-- Header Title -->
    <div class="text-center max-w-3xl mx-auto space-y-4 reveal">
      <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full border border-[#e5c158]/30 bg-[#e5c158]/5 text-xs text-[#e5c158] font-semibold tracking-wider uppercase">
        <i data-lucide="image" class="w-3.5 h-3.5"></i>
        <span>Gallery archives</span>
      </div>
      <h1 class="font-serif text-4xl sm:text-5xl font-bold text-white">
        Bespoke <span class="text-gold-gradient">Visual Portfolio</span>
      </h1>
      <p class="text-sm text-gray-400 font-light max-w-xl mx-auto leading-relaxed">
        Browse through our curated collection of editorial fashion shoots, intimate portraits, and timeless wedding stories. Click any image to view details in high resolution.
      </p>
    </div>

    <!-- Category Filter Bar (Alpine.js) -->
    <div class="flex flex-wrap items-center justify-center gap-3 py-4 border-y border-white/5 reveal">
      <button @click="activeCategory = 'All'" 
              :class="activeCategory === 'All' ? 'bg-[#e5c158] text-black' : 'border border-white/10 text-white/80 hover:border-white/30 hover:text-white'"
              class="px-5 py-2 text-xs font-semibold uppercase tracking-wider rounded-full transition-all duration-300">
        All Creations
      </button>
      @foreach($categories as $cat)
        <button @click="activeCategory = '{{ $cat }}'" 
                :class="activeCategory === '{{ $cat }}' ? 'bg-[#e5c158] text-black' : 'border border-white/10 text-white/80 hover:border-white/30 hover:text-white'"
                class="px-5 py-2 text-xs font-semibold uppercase tracking-wider rounded-full transition-all duration-300">
          {{ $cat }}
        </button>
      @endforeach
    </div>

    <!-- Gallery Grid with Filter Animation -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 reveal">
      @foreach($galleryItems as $idx => $item)
        <div x-show="activeCategory === 'All' || activeCategory === '{{ $item->category }}'"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="glass-panel hover-zoom-img-parent rounded-2xl overflow-hidden group border border-white/10 cursor-pointer"
             @click="openLightbox('{{ $item->image_path }}', '{{ $item->title }}', '{{ $item->category }}')">
          <div class="relative aspect-square w-full overflow-hidden">
            <img src="{{ $item->image_path }}" alt="{{ $item->title }}" class="w-full h-full object-cover hover-zoom-img">
            <!-- Overlay indicator -->
            <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-6">
              <span class="text-xs uppercase tracking-widest text-[#e5c158] mb-1 font-semibold">{{ $item->category }}</span>
              <h3 class="font-serif text-lg font-bold text-white leading-snug">{{ $item->title }}</h3>
              <span class="inline-flex items-center text-xs text-white/60 mt-3">
                <i data-lucide="maximize-2" class="w-3.5 h-3.5 mr-1.5 text-[#e5c158]"></i> Inspect Plate
              </span>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <!-- Lightbox Pop-up Modal (Alpine.js) -->
    <div x-show="lightboxOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/95 backdrop-blur-md"
         style="display: none;"
         @keydown.escape.window="lightboxOpen = false">
      
      <!-- Close button on overlay -->
      <button @click="lightboxOpen = false" 
              class="absolute top-6 right-6 text-white/50 hover:text-white bg-white/10 hover:bg-white/20 p-3 rounded-full transition-all">
        <i data-lucide="x" class="w-6 h-6"></i>
      </button>

      <div class="max-w-4xl w-full flex flex-col space-y-4" @click.away="lightboxOpen = false">
        <div class="aspect-auto max-h-[75vh] w-full rounded-2xl overflow-hidden border border-white/15 bg-black flex items-center justify-center">
          <img :src="lightboxImg" :alt="lightboxTitle" class="max-h-[75vh] max-w-full object-contain">
        </div>
        <div class="flex items-center justify-between text-left px-2">
          <div>
            <span class="text-xs uppercase tracking-widest text-[#e5c158] font-bold" x-text="lightboxCat"></span>
            <h3 class="font-serif text-xl sm:text-2xl font-bold text-white mt-1" x-text="lightboxTitle"></h3>
          </div>
          <button @click="lightboxOpen = false" class="px-6 py-2 border border-white/10 hover:border-white/20 bg-white/5 hover:bg-white/10 text-xs uppercase tracking-widest font-semibold rounded-full transition-all">
            Close View
          </button>
        </div>
      </div>
    </div>

  </div>
</div>
@endsection
