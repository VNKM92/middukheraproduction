@extends('layouts.app')

@section('content')
<div class="relative overflow-hidden">
  <!-- Glowing Background Orbs -->
  <div class="hero-glow top-10 left-10"></div>
  <div class="hero-glow-gold bottom-40 right-10"></div>

  <!-- Hero Header Block -->
  <section class="relative pt-12 pb-8 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto z-10">
    <div class="space-y-6 text-center reveal">
      <a href="{{ route('blog') }}" class="inline-flex items-center text-xs uppercase tracking-widest text-[#e5c158] hover:text-white transition-colors group">
        <i data-lucide="arrow-left" class="w-4 h-4 mr-2 group-hover:-translate-x-1 transition-transform"></i>
        <span>Back to Journals</span>
      </a>
      
      <h1 class="font-serif text-3xl sm:text-4xl md:text-5xl font-bold text-white leading-tight">
        {{ $blog->title }}
      </h1>

      <div class="flex items-center justify-center space-x-6 text-xs text-white/50 border-y border-white/5 py-4 max-w-xl mx-auto">
        <div class="flex items-center"><i data-lucide="calendar" class="w-4 h-4 mr-2 text-[#e5c158]"></i> {{ $blog->created_at ? $blog->created_at->format('M d, Y') : 'Aug 11, 2026' }}</div>
        <div class="flex items-center"><i data-lucide="clock" class="w-4 h-4 mr-2 text-[#e5c158]"></i> 5 Min Read</div>
        <div class="flex items-center"><i data-lucide="award" class="w-4 h-4 mr-2 text-[#e5c158]"></i> Masterclass</div>
      </div>
    </div>

    <!-- Big Feature Image -->
    <div class="mt-8 rounded-3xl overflow-hidden border border-white/10 p-2 bg-[#111019]/60 backdrop-blur-md aspect-video max-w-5xl mx-auto reveal reveal-scale">
      <img src="{{ $blog->image_path }}" alt="{{ $blog->title }}" class="w-full h-full object-cover rounded-2xl">
    </div>
  </section>

  <!-- Article Content & Author Card -->
  <section class="pb-20 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto relative z-10">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
      <!-- Main Article Text -->
      <div class="lg:col-span-8 space-y-6 text-gray-300 font-light leading-relaxed text-base sm:text-lg reveal">
        <div class="prose prose-invert max-w-none prose-headings:font-serif prose-headings:text-white prose-a:text-[#e5c158] prose-strong:text-white">
          {!! $blog->content !!}
        </div>
      </div>

      <!-- Author Bio Sidebar -->
      <div class="lg:col-span-4 space-y-6 reveal reveal-right">
        <div class="glass-panel rounded-3xl p-6 border border-white/10 text-center space-y-4">
          <div class="w-20 h-20 rounded-full bg-[#e5c158] text-black font-bold flex items-center justify-center text-xl mx-auto shadow-lg border-2 border-white/20">
            LM
          </div>
          <div>
            <h4 class="font-serif text-lg font-bold text-white">Middukhera Master</h4>
            <span class="text-xs uppercase tracking-widest text-[#e5c158] font-semibold">Principal Writer</span>
          </div>
          <p class="text-xs text-gray-400 font-light leading-relaxed">
            Our editorial articles are written by resident creative directors, documenting real set layouts, fashion wardrobes, and camera mechanics.
          </p>
          <div class="flex items-center justify-center space-x-3 pt-2">
            <a href="#" class="text-white/40 hover:text-[#e5c158] transition-colors"><i data-lucide="instagram" class="w-4 h-4"></i></a>
            <a href="#" class="text-white/40 hover:text-[#e5c158] transition-colors"><i data-lucide="linkedin" class="w-4 h-4"></i></a>
            <a href="#" class="text-white/40 hover:text-[#e5c158] transition-colors"><i data-lucide="mail" class="w-4 h-4"></i></a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Related/Recent Stories -->
  <section class="py-16 bg-[#111019]/20 border-t border-white/5 relative z-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="text-center max-w-2xl mx-auto mb-12 reveal">
        <h2 class="font-serif text-3xl font-bold text-white">Recommended Reading</h2>
        <div class="w-16 h-0.5 bg-gradient-to-r from-transparent via-[#e5c158] to-transparent mx-auto mt-2"></div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @foreach($recentBlogs as $idx => $b)
          <article class="glass-panel hover-zoom-img-parent rounded-3xl overflow-hidden group hover:border-white/15 transition-all duration-300 flex flex-col justify-between reveal" style="transition-delay: {{ $idx * 100 }}ms">
            <div>
              <div class="aspect-video w-full overflow-hidden border-b border-white/5">
                <img src="{{ $b->image_path }}" class="w-full h-full object-cover hover-zoom-img">
              </div>
              <div class="p-6 space-y-4">
                <h3 class="font-serif text-lg font-bold text-white group-hover:text-[#e5c158] transition-colors leading-snug">
                  <a href="{{ route('blog.single', $b->slug) }}">{{ $b->title }}</a>
                </h3>
                <p class="text-xs text-gray-400 font-light leading-relaxed">{{ Str::limit($b->excerpt, 100) }}</p>
              </div>
            </div>
            <div class="px-6 pb-6 pt-2">
              <a href="{{ route('blog.single', $b->slug) }}" class="inline-flex items-center text-xs font-bold text-white group-hover:text-[#e5c158] transition-colors">
                <span>Read Story</span>
                <i data-lucide="chevron-right" class="w-4 h-4 ml-0.5 group-hover:translate-x-1 transition-transform"></i>
              </a>
            </div>
          </article>
        @endforeach
      </div>
    </div>
  </section>
</div>
@endsection
