@php
  $brand = '#eb3d22';
@endphp

<div class="mx-auto max-w-5xl">

  <h1 class="text-3xl font-extrabold mb-4">Your Cart</h1>

  @if (session('ok'))
    <div class="mb-4 text-sm text-green-700 bg-green-50 border border-green-200 rounded-lg p-3">
      {{ session('ok') }}
    </div>
  @endif

  {{-- container is capped to viewport height --}}
  <div class="bg-white rounded-2xl shadow border border-neutral-200 overflow-hidden">

    {{-- list (scrolls if too tall) --}}
    <div class="max-h-[calc(100vh-260px)] overflow-y-auto divide-y">
      @forelse ($items as $i)
        @php
          $src = $i['image']
            ? (\Illuminate\Support\Str::startsWith($i['image'], ['http://','https://']) ? $i['image'] : asset($i['image']))
            : asset('img/placeholder.png');
        @endphp

        <div class="flex items-center gap-4 p-4 {{ $i['available'] ? '' : 'bg-red-50' }}">
          <img src="{{ $src }}" class="h-16 w-16 rounded-lg object-cover border" alt="">
          <div class="flex-1">
            <div class="font-semibold">{{ $i['name'] }}</div>
            @if(!$i['available'])
              <div class="text-xs text-red-600">Unavailable</div>
            @endif
            @if($i['available'])
              <div class="text-xs text-neutral-500">Rs {{ number_format($i['price_each'],2) }} each</div>
            @endif
          </div>

          @if ($i['available'])
            <div class="flex items-center gap-2">
              <button wire:click="dec({{ $i['id'] }})"
                      class="h-8 w-8 rounded-md border border-neutral-300 hover:bg-neutral-100">–</button>
              <div class="w-10 text-center">{{ $i['quantity'] }}</div>
              <button wire:click="inc({{ $i['id'] }})"
                      class="h-8 w-8 rounded-md border border-neutral-300 hover:bg-neutral-100">+</button>
            </div>
            <div class="w-28 text-right font-semibold">
              Rs {{ number_format($i['line'], 2) }}
            </div>
          @else
            <div class="w-28 text-right font-semibold text-neutral-400">—</div>
          @endif

          <button wire:click="remove({{ $i['id'] }})"
                  class="ml-3 text-red-600 hover:underline">Remove</button>
        </div>
      @empty
        <div class="p-8 text-center text-neutral-500">Your cart is empty.</div>
      @endforelse
    </div>

    {{-- footer --}}
    <div class="flex items-center justify-between p-4">
      <div class="text-lg">
        <span class="text-neutral-500">Total:</span>
        <span class="font-extrabold">Rs {{ number_format($total, 2) }}</span>
      </div>
      <button wire:click="checkout"
              @disabled($total <= 0)
              class="px-6 py-3 rounded-md text-white font-semibold disabled:opacity-50"
              style="background: {{ $brand }};">
        Checkout
      </button>
    </div>
  </div>

</div>
