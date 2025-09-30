<x-site.layout title="Urban Roast • Home">

<div class="h-screen overflow-y-scroll snap-y snap-mandatory hide-scrollbar">
  <section class="pt-8 lg:pt-14 grid grid-cols-1 lg:grid-cols-2 gap-10 items-center animate-slide-fade-in snap-start">
    {{-- Left copy --}}
    <div class="order-2 lg:order-1">
      <div class="inline-flex items-center gap-3 mb-4">
        <span class="text-[#eb3d22] font-bold tracking-wide text-5xl">コーヒー</span>
        
      </div>
      <br>
    <div class="w-[40vh] h-1 bg-neutral-700"></div>
    <br>

      <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold leading-tight">
        A CUP OF LOVE FROM <span class="text-[#eb3d22]">SPECIALTY COFFEE</span> FOR YOU
      </h1>

      <p class="mt-5 max-w-xl text-[15px] leading-relaxed text-neutral-700">
        Hand-crafted espresso, slow-brewed perfection. Urban Roast serves freshly roasted beans
        and signature drinks made to comfort and energize— from rich espresso shots to silky lattes and cold brews.
      </p>

      <div class="mt-7">
        <a href="{{ url('/shop') }}"
           class="inline-flex items-center justify-center px-6 py-3 rounded-md bg-[#eb3d22] text-white font-medium shadow hover:bg-[#5A3F2D] transition">
          Order Now
        </a>
      </div>
    </div>
    

    {{-- Right image --}}
    <div class="order-1 lg:order-2">
      <img src="{{ asset('img/hero-coffee.png') }}" alt="Coffee hero"
           class="w-full h-auto drop-shadow-xl rounded-md"
           onerror="this.src='{{ asset('img/coffee_hero_image.png') }}'">
    </div>
  
  </section>


   <section class="mt-12 animate-slide-fade-in h-screen snap-start">
    <livewire:shop.menu-grid />
  </section>


 </div>   
</x-site.layout>

