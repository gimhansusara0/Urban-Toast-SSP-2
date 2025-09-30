@php
  $brand = '#eb3d22';        // buttons
  $bg    = '#ebe0ce';        // page bg (used elsewhere)
@endphp

<div id="menu-grid" class="space-y-6">

  <p class="text-5xl">Menu <span class="text-5xl text-[#eb3d22]">メニュー</span></p>

  {{-- Category bar (horizontal scroll on overflow) --}}
  <div class="relative">
    <div class="flex gap-2 overflow-x-auto whitespace-nowrap p-1 rounded-2xl bg-white shadow border border-neutral-200">
      <button wire:click="selectCategory('all')"
        class="px-4 py-2 rounded-full text-sm font-medium transition {{ $category==='all' ? 'text-white' : 'text-neutral-700 hover:text-neutral-900' }}"
        style="background: {{ $category==='all' ? $brand : 'transparent' }}; border:1px solid {{ $category==='all' ? 'transparent' : 'rgba(0,0,0,0.1)' }};">
        All
      </button>
      @foreach($categories as $c)
        @php $active = (string)$c['id'] === (string)$category; @endphp
        <button wire:click="selectCategory('{{ $c['id'] }}')"
          class="px-4 py-2 rounded-full text-sm font-medium transition {{ $active ? 'text-white' : 'text-neutral-700 hover:text-neutral-900' }}"
          style="background: {{ $active ? $brand : 'transparent' }}; border:1px solid {{ $active ? 'transparent' : 'rgba(0,0,0,0.1)' }};">
          {{ $c['name'] }}
        </button>
      @endforeach
    </div>
  </div>

  {{-- Product grid (image covers the card, controls at bottom) --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse ($products as $p)
      @php
        $src = $p->image
            ? (\Illuminate\Support\Str::startsWith($p->image, ['http://','https://']) ? $p->image : asset($p->image))
            : asset('img/placeholder.png');
        $in = in_array($p->id, $inCart ?? [], true);
      @endphp

      <div class="relative rounded-2xl overflow-hidden shadow-lg bg-white">
        {{-- full image --}}
        <img src="{{ $src }}" alt="{{ $p->name }}" class="w-full h-56 sm:h-64 object-cover">

        {{-- name badge (top-left) --}}
        <div class="absolute top-3 left-3 bg-black/70 text-white text-xs font-semibold px-3 py-1 rounded-full">
          {{ $p->name }}
        </div>

        {{-- bottom bar: price + add button --}}
        <div class="flex items-center justify-between gap-2 px-4 py-3 bg-neutral-700"
             >
          <div class="text-white font-bold">
            Rs {{ number_format((float)$p->price, 2) }}
          </div>

          @if ($in)
            <button disabled class="px-4 py-2 rounded-md bg-white/25 text-white font-medium cursor-not-allowed">
              In cart
            </button>
          @else
            <button wire:click="addToCart({{ $p->id }})"
                    class="px-4 py-2 rounded-md bg-[#eb3d22] text-white font-semibold hover:opacity-90 transition">
              Add to cart
            </button>
          @endif
        </div>
      </div>
    @empty
      <div class="col-span-full">
        <div class="bg-white rounded-2xl border border-neutral-200 p-8 text-center text-neutral-600">
          No products found for this category.
        </div>
      </div>
    @endforelse
  </div>

  {{-- Button-based pagination (no URL changes) --}}
  @php
    $current = $products->currentPage();
    $last    = $products->lastPage();
    $start   = max(1, $current - 2);
    $end     = min($last, $current + 2);
  @endphp

  @if ($last > 1)
    <div class="flex items-center justify-center gap-1">
      <button wire:click="goPrev" @disabled($current === 1)
              class="px-3 py-2 rounded-md border border-neutral-300 text-sm disabled:opacity-50 hover:bg-neutral-100">
        ‹ Prev
      </button>

      @if ($start > 1)
        <button wire:click="goTo(1)" class="px-3 py-2 rounded-md border border-neutral-300 text-sm hover:bg-neutral-100">1</button>
        @if ($start > 2) <span class="px-2 text-neutral-500">…</span> @endif
      @endif

      @for ($i = $start; $i <= $end; $i++)
        <button wire:click="goTo({{ $i }})"
                class="px-3 py-2 rounded-md text-sm {{ $i === $current ? 'text-white' : 'border border-neutral-300 hover:bg-neutral-100' }}"
                style="background: {{ $i === $current ? $brand : 'transparent' }};">
          {{ $i }}
        </button>
      @endfor

      @if ($end < $last)
        @if ($end < $last - 1) <span class="px-2 text-neutral-500">…</span> @endif
        <button wire:click="goTo({{ $last }})" class="px-3 py-2 rounded-md border border-neutral-300 text-sm hover:bg-neutral-100">{{ $last }}</button>
      @endif

      <button wire:click="goNext" @disabled($current === $last)
              class="px-3 py-2 rounded-md border border-neutral-300 text-sm disabled:opacity-50 hover:bg-neutral-100">
        Next ›
      </button>
    </div>
  @endif
</div>

@once
  @push('scripts')
    <script>
      window.addEventListener('menu-grid-scroll', () => {
        const el = document.getElementById('menu-grid');
        if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
      });
    </script>
  @endpush
@endonce
