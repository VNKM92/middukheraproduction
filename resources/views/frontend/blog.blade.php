@extends('layouts.app')

@section('content')
<div class="relative overflow-hidden py-16 px-4 sm:px-6 lg:px-8">
  <!-- Glowing Background Orbs -->
  <div class="hero-glow top-20 right-10"></div>
  <div class="hero-glow-gold bottom-20 left-10"></div>

  <div class="max-w-7xl mx-auto relative z-10 space-y-16">
    <!-- Header Title -->
    <div class="text-center max-w-3xl mx-auto space-y-4 reveal">
      <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full border border-[#e5c158]/30 bg-[#e5c158]/5 text-xs text-[#e5c158] font-semibold tracking-wider uppercase">
        <i data-lucide="book-open" class="w-3.5 h-3.5"></i>
        <span>Middukhera Chronicles</span>
      </div>
      <h1 class="font-serif text-4xl sm:text-5xl font-bold text-white">
        The Art of <span class="text-gold-gradient">Visual Storytelling</span>
      </h1>
      <p class="text-sm text-gray-400 font-light max-w-xl mx-auto leading-relaxed">
        Read guides from our elite stylists, light setup walk-throughs from our principal photographers, and behind-the-scenes diaries from couture runways.
      </p>
    </div>

    <!-- Featured Post (Display the first post on page 1) -->
    @if($blogs->count() > 0 && $blogs->currentPage() == 1)
      @php $featured = $blogs->first(); @endphp
      <div class="glass-panel rounded-3xl overflow-hidden grid grid-cols-1 lg:grid-cols-12 gap-8 p-6 sm:p-8 hover:border-white/15 transition-all duration-300 reveal reveal-scale">
        <div class="lg:col-span-7 aspect-video w-full rounded-2xl overflow-hidden border border-white/5 hover-zoom-img-parent">
          <img src="{{ $featured->image_path }}" alt="{{ $featured->title }}" class="w-full h-full object-cover hover-zoom-img">
        </div>
        <div class="lg:col-span-5 flex flex-col justify-between py-4">
          <div class="space-y-4">
            <div class="inline-flex items-center space-x-2 text-xs text-[#e5c158] font-bold tracking-widest uppercase">
              <span class="bg-[#e5c158]/10 px-2.5 py-1 rounded-md border border-[#e5c158]/20">Featured Story</span>
              <span>•</span>
              <span>5 min read</span>
            </div>
            <h2 class="font-serif text-2xl sm:text-3xl font-bold text-white hover:text-[#e5c158] transition-colors leading-tight">
              <a href="{{ route('blog.single', $featured->slug) }}">{{ $featured->title }}</a>
            </h2>
            <p class="text-sm text-gray-400 font-light leading-relaxed">
              {{ $featured->excerpt }}
            </p>
          </div>
          
          <div class="pt-6 border-t border-white/5 flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div class="w-10 h-10 rounded-full bg-[#e5c158] text-black font-bold flex items-center justify-center text-xs shadow-md">
                LM
              </div>
              <div>
                <span class="text-xs font-bold text-white block">Middukhera Master</span>
                <span class="text-[10px] text-white/50">Principal Photographer</span>
              </div>
            </div>
            <a href="{{ route('blog.single', $featured->slug) }}" class="inline-flex items-center text-[#e5c158] hover:text-white transition-colors text-sm font-semibold group">
              <span>Read Masterclass</span>
              <i data-lucide="arrow-right" class="w-4 h-4 ml-1.5 group-hover:translate-x-1 transition-transform"></i>
            </a>
          </div>
        </div>
      </div>
    @endif

    <!-- Blog Grid List -->
    <div class="space-y-8">
      <h3 class="font-serif text-2xl font-bold text-white pl-2">All Articles</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($blogs as $idx => $post)
          <!-- Skip first post on first page since it is featured -->
          @if($blogs->currentPage() == 1 && $idx === 0)
            @continue
          @endif
          <article class="glass-panel hover-zoom-img-parent rounded-3xl overflow-hidden group hover:border-white/15 transition-all duration-300 flex flex-col justify-between reveal" style="transition-delay: {{ $idx * 80 }}ms">
            <div>
              <div class="aspect-video w-full overflow-hidden border-b border-white/5">
                <img src="{{ $post->image_path }}" class="w-full h-full object-cover hover-zoom-img">
              </div>
              <div class="p-6 space-y-4">
                <div class="flex items-center space-x-2 text-xs text-[#e5c158] font-semibold tracking-widest uppercase">
                  <span>Photography</span>
                  <span>•</span>
                  <span>4 min read</span>
                </div>
                <h3 class="text-xl font-serif font-bold text-white group-hover:text-[#e5c158] transition-colors leading-snug">
                  <a href="{{ route('blog.single', $post->slug) }}">{{ $post->title }}</a>
                </h3>
                <p class="text-sm text-gray-400 font-light leading-relaxed">{{ Str::limit($post->excerpt, 120) }}</p>
              </div>
            </div>
            
            <div class="px-6 pb-6 pt-2 border-t border-white/5 mt-4 flex items-center justify-between">
              <div class="flex items-center space-x-2">
                <div class="w-8 h-8 rounded-full bg-[#8b5cf6]/20 border border-[#8b5cf6]/35 text-white flex items-center justify-center text-xs font-bold">
                  LA
                </div>
                <span class="text-xs text-white/70">Middukhera Artist</span>
              </div>
              <a href="{{ route('blog.single', $post->slug) }}" class="inline-flex items-center text-xs font-bold text-white group-hover:text-[#e5c158] transition-colors">
                <span>Read Story</span>
                <i data-lucide="chevron-right" class="w-4 h-4 ml-0.5 group-hover:translate-x-1 transition-transform"></i>
              </a>
            </div>
          </article>
        @endforeach
      </div>
    </div>

    <!-- Pagination links -->
    <div class="pt-8 border-t border-white/5 reveal">
      {{ $blogs->links() }}
    </div>

  </div>
</div>
@endsection
