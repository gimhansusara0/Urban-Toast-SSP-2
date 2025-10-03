<x-site.layout title="About Us • Urban Roast">
  <!-- Hero -->
  <section class="relative py-20 bg-cover bg-center">
    <div class="relative max-w-4xl mx-auto text-center text-white space-y-6">
      <h1 class="text-5xl font-extrabold">About <span class="text-[#DB6246]">Urban Roast</span></h1>
      <br><br>
    </div>
  </section>

  <!-- Content -->
  <section class="py-20 max-w-6xl mx-auto grid md:grid-cols-2 gap-10 px-6">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
      <img src="https://images.pexels.com/photos/414628/pexels-photo-414628.jpeg"
           alt="Our story" class="w-full h-48 object-cover">
      <div class="p-8">
        <h2 class="text-2xl font-bold text-neutral-800 mb-4">☕ Our Story</h2>
        <p class="text-neutral-600 leading-relaxed">
          Urban Roast began with a spark: a love for artisanal coffee and a desire
          to create a warm space for everyone. From a small café, we’ve grown into
          a place where coffee lovers, creatives, and friends gather.
        </p>
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
      <img src="https://images.pexels.com/photos/302903/pexels-photo-302903.jpeg"
           alt="Our mission" class="w-full h-48 object-cover">
      <div class="p-8">
        <h2 class="text-2xl font-bold text-neutral-800 mb-4">🌱 Our Mission</h2>
        <p class="text-neutral-600 leading-relaxed">
          We believe in <span class="text-[#DB6246] font-semibold">quality, comfort, and connection</span>.
          Whether it’s your quick espresso or a long chat, every sip should feel like home.
        </p>
      </div>
    </div>
  </section>

  <!-- Reviews link -->
  <section class="py-10 text-center">
    <p class="text-neutral-600">
      Curious what others think about us?  
      <a href="{{ url('/reviews') }}" class="text-[#DB6246] font-medium hover:underline">
        Read our customer reviews →
      </a>
    </p>
  </section>
</x-site.layout>
