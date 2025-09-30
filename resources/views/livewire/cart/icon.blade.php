@php $brand = '#eb3d22'; @endphp
<button wire:click="go" class="relative inline-flex items-center justify-center p-2 rounded-full hover:bg-black/5">
  {{-- cart icon --}}
  <svg class="h-6 w-6 text-neutral-700" viewBox="0 0 24 24" fill="none" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
          d="M3 3h2l.4 2M7 13h10l3-8H6.4M7 13L5.4 5M7 13l-2 9m2-9l2 9m8-9l2 9m-7-5h2"/>
  </svg>
  @if($count > 0)
    <span class="absolute -top-1 -right-1 min-w-[1.25rem] h-5 px-1 rounded-full text-[10px] font-bold text-white flex items-center justify-center"
          style="background: {{ $brand }};">
      {{ $count }}
    </span>
  @endif
</button>
