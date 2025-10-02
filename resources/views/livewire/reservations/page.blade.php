@php
  $bgMain = 'bg-[#ebe0ce]';           // main background
  $textMain = 'text-[#333333]';       // main text
  $highlight = 'bg-[#DB6246] text-white hover:bg-[#c8533a]'; // highlight
  $highlightBorder = 'border-[#DB6246] text-[#DB6246] hover:bg-[#DB6246]/10'; // outline
@endphp

<div class="w-full h-screen max-h-screen flex items-center justify-center {{ $bgMain }}">
  <div class="w-full max-w-3xl mx-auto px-4">
    <div class="bg-white/90 backdrop-blur-md border border-[#DB6246]/20 rounded-2xl shadow-lg overflow-hidden">
      
      {{-- Header --}}
      <div class="p-4 sm:p-6 border-b border-[#DB6246]/20 flex items-center justify-between">
        <div>
          <h1 class="text-xl sm:text-2xl font-semibold {{ $textMain }}">Reservations</h1>
          <p class="text-sm text-[#555]">Book a table and manage your bookings.</p>
        </div>

        {{-- Panel switcher --}}
        <div class="inline-flex rounded-xl bg-[#ebe0ce] p-1">
          <button wire:click="switchPanel('create')"
            class="px-3 sm:px-4 py-2 rounded-lg text-sm font-medium transition
              {{ $panel === 'create' ? 'bg-white shadow '.$textMain : 'text-[#555]' }}">
            New Reservation
          </button>
          <button wire:click="switchPanel('list')"
            class="px-3 sm:px-4 py-2 rounded-lg text-sm font-medium transition
              {{ $panel === 'list' ? 'bg-white shadow '.$textMain : 'text-[#555]' }}">
            My Reservations
          </button>
        </div>
      </div>

      {{-- Content --}}
      <div class="p-4 sm:p-6">
        {{-- Create Reservation Panel --}}
        @if ($panel === 'create')
          <div class="grid gap-4 sm:gap-6 sm:grid-cols-2">
            <div class="sm:col-span-2">
              <label class="block text-sm font-medium {{ $textMain }} mb-1">My name</label>
              <input type="text" wire:model.defer="name"
                class="w-full rounded-xl border border-[#DB6246]/30 focus:ring-2 focus:ring-[#DB6246] focus:border-[#DB6246]">
              @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
              <label class="block text-sm font-medium {{ $textMain }} mb-1">Contact number</label>
              <input type="text" wire:model.defer="contact"
                class="w-full rounded-xl border border-[#DB6246]/30 focus:ring-2 focus:ring-[#DB6246] focus:border-[#DB6246]">
              @error('contact') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
              <label class="block text-sm font-medium {{ $textMain }} mb-1">Date</label>
              <input type="date" wire:model.defer="date" min="{{ $today }}"
                class="w-full rounded-xl border border-[#DB6246]/30 focus:ring-2 focus:ring-[#DB6246] focus:border-[#DB6246]">
              @error('date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
              <label class="block text-sm font-medium {{ $textMain }} mb-1">Time</label>
              <input type="time" wire:model.defer="time"
                class="w-full rounded-xl border border-[#DB6246]/30 focus:ring-2 focus:ring-[#DB6246] focus:border-[#DB6246]">
              @error('time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-2 flex justify-end">
              <button wire:click="save" wire:loading.attr="disabled"
                class="inline-flex items-center justify-center px-5 py-3 rounded-xl font-medium transition disabled:opacity-60 {{ $highlight }}">
                <svg wire:loading wire:target="save" class="animate-spin mr-2 h-4 w-4" viewBox="0 0 24 24" fill="none">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                </svg>
                Book Reservation
              </button>
            </div>
          </div>

        {{-- List Reservations Panel --}}
        @else
          <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-1">
            @forelse ($myReservations as $r)
              <div class="flex items-center justify-between p-4 bg-[#ebe0ce]/40 border border-[#DB6246]/20 rounded-xl">
                <div>
                  <p class="text-sm text-[#666]">Date & Time</p>
                  <p class="font-semibold {{ $textMain }}">{{ $r['date'] }} at {{ $r['time'] }}</p>
                </div>
                <div class="hidden sm:block">
                  <p class="text-sm text-[#666]">Status</p>
                  <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium
                    @if($r['status']==='pending') bg-yellow-200 text-yellow-800
                    @elseif(str_starts_with($r['status'],'canceled')) bg-red-200 text-red-800
                    @else bg-green-200 text-green-800 @endif">
                    {{ ucfirst(str_replace('_',' ',$r['status'])) }}
                  </span>
                </div>
                <div class="flex items-center gap-2">
                  @if($r['status']==='pending')
                    <button wire:click="cancel('{{ $r['id'] }}')" class="px-3 py-2 text-sm rounded-lg {{ $highlightBorder }}">
                      Cancel
                    </button>
                  @endif
                </div>
              </div>
            @empty
              <div class="p-6 text-center text-[#666]">
                No reservations yet. Create your first one!
              </div>
            @endforelse
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
