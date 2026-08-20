@php
    // Define an array of image URLs for the slider. Replace with real images as needed.
    $images = [
        'https://picsum.photos/seed/slide1/1200/400',
        'https://picsum.photos/seed/slide2/1200/400',
        'https://picsum.photos/seed/slide3/1200/400',
    ];
@endphp

<div class="relative overflow-hidden rounded-lg shadow-lg" id="hero-slider">
    <div class="flex transition-transform duration-700 ease-in-out" style="transform: translateX(0%);" x-data="{ current: 0, slides: {{ count($images) }} }" x-init="setInterval(() => { current = (current + 1) % slides; $el.style.transform = `translateX(-${current * 100}% )` }, 5000)">
        @foreach ($images as $img)
            <div class="min-w-full">
                <img src="{{ $img }}" alt="Slide {{ $loop->iteration }}" class="w-full h-64 object-cover" />
            </div>
        @endforeach
    </div>
    <!-- Navigation Dots -->
    <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
        @for ($i = 0; $i < count($images); $i++)
            <button @click="$el.parentElement.parentElement.style.transform = `translateX(-${$i * 100}% )`" class="w-3 h-3 rounded-full bg-white opacity-70 hover:opacity-100 focus:outline-none"></button>
        @endfor
    </div>
</div>
