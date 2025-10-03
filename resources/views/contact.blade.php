<x-site.layout title="Contact Us • Urban Roast">
  <section class="relative min-h-screen flex flex-col">
    <!-- Hero -->
    <div class="pt-12 pb-8 text-center">
      <h1 class="text-5xl font-extrabold text-neutral-800">Get in Touch</h1>
      <p class="text-lg max-w-2xl mx-auto text-neutral-700 mt-4">
        Questions, feedback, or collaborations — our team would love to hear from you.
      </p>
    </div>

    <!-- Content (fills remaining height) -->
    <div class="flex-1 flex items-center justify-center">
      <div class="grid md:grid-cols-2 gap-12 max-w-6xl w-full px-6">
        <!-- Form -->
        <div class="bg-white rounded-2xl shadow-xl p-10 hover:shadow-2xl transition">
          <h2 class="text-2xl font-bold text-neutral-800 mb-6">Send us a message</h2>
          <form action="#" method="POST" class="space-y-5">
            @csrf
            <div>
              <label class="block text-sm font-medium text-neutral-700">Name</label>
              <input type="text" name="name" required
                     class="mt-1 w-full rounded-lg border border-neutral-300 px-4 py-2 focus:ring-2 focus:ring-[#DB6246]">
            </div>
            <div>
              <label class="block text-sm font-medium text-neutral-700">Email</label>
              <input type="email" name="email" required
                     class="mt-1 w-full rounded-lg border border-neutral-300 px-4 py-2 focus:ring-2 focus:ring-[#DB6246]">
            </div>
            <div>
              <label class="block text-sm font-medium text-neutral-700">Message</label>
              <textarea name="message" rows="4" required
                        class="mt-1 w-full rounded-lg border border-neutral-300 px-4 py-2 focus:ring-2 focus:ring-[#DB6246]"></textarea>
            </div>
            <button type="submit"
                    class="w-full rounded-lg bg-[#DB6246] text-white px-4 py-2 font-medium hover:bg-[#c5533c] transition">
              Send Message
            </button>
          </form>
        </div>

        <!-- Contact info -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
          <img src="https://images.pexels.com/photos/2836945/pexels-photo-2836945.jpeg"
               alt="Coffee shop" class="w-full h-28 object-cover">
          <div class="p-8 space-y-6">
            <div>
              <h3 class="font-semibold text-neutral-800">📍 Visit Us</h3>
              <p class="text-neutral-600">123 Brew Street, Colombo, Sri Lanka</p>
            </div>
            <div>
              <h3 class="font-semibold text-neutral-800">📞 Call Us</h3>
              <p class="text-neutral-600">+94 71 234 5678</p>
            </div>
            <div>
              <h3 class="font-semibold text-neutral-800">✉️ Email</h3>
              <p class="text-neutral-600">ummtest@urbanroast.com</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</x-site.layout>
