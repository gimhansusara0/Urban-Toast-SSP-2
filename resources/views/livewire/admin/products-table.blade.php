@php
  use Illuminate\Support\Str;
@endphp

<div class="space-y-4"><!-- single root -->

  {{-- Header / Tools --}}
  <div class="bg-white rounded-2xl shadow p-4">
    <form wire:submit.prevent="applySearch" class="flex flex-col md:flex-row md:items-center gap-3">
      <div class="flex-1">
        <input
          type="text"
          wire:model.defer="searchInput"
          wire:keydown.enter="applySearch"
          placeholder="Search products by name…"
          class="w-full rounded-xl border border-neutral-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#6B4F3A]">
      </div>


      <button type="submit" class="rounded-xl px-4 py-2.5 bg-blue-500 text-white hover:bg-blue-600">
        Search
      </button>

      {{-- Category filter --}}
      <select
        wire:change="changeCategory($event.target.value)"
        class="rounded-xl border border-neutral-300 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#6B4F3A]">
        <option value="all" @selected($category==='all')>Category</option>
        @foreach ($categories as $cat)
          <option value="{{ $cat->id }}" @selected((string)$cat->id === (string)$category)>{{ $cat->name }}</option>
        @endforeach
      </select>

      {{-- Availability (status) --}}
      <select
        wire:change="changeStatus($event.target.value)"
        class="rounded-xl border border-neutral-300 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#6B4F3A]">
        <option value="all" @selected($status==='all')>All</option>
        <option value="active" @selected($status==='active')>Available</option>
        <option value="inactive" @selected($status==='inactive')>Unavailable</option>
        <option value="archived" @selected($status==='archived')>Archived</option>
      </select>

      

     

      <button type="button" wire:click="bulkDeactivate" class="rounded-xl px-4 py-2.5 border border-neutral-300 hover:bg-neutral-100">
        Update Status
      </button>

       <button type="button" wire:click="openCreate" class="rounded-xl px-4 py-2.5 border text-white border-neutral-300 bg-green-500 hover:bg-green-600">
        Add
      </button>

      <button type="button" wire:click="bulkRemove" class="rounded-xl px-4 py-2.5 bg-red-600 text-white hover:bg-red-500">
        Remove
      </button>
    </form>

    @if (session('ok'))
      <div class="mt-3 text-sm text-green-700 bg-green-50 border border-green-200 rounded-lg p-3">
        {{ session('ok') }}
      </div>
    @endif
  </div>

  {{-- Table (fixed visual height + pagination) --}}
  <div class="bg-white rounded-2xl shadow overflow-hidden min-h-[520px] flex flex-col">
    <table class="w-full text-sm">
      <thead class="bg-neutral-50 text-neutral-600">
        <tr>
          <th class="p-3 w-10">
            <input type="checkbox" wire:click="toggleSelectAll">
          </th>
          <th class="p-3 text-left">Image</th>
          <th class="p-3 text-left">Name</th>
          <th class="p-3 text-left">Category</th>
          <th class="p-3 text-left">Price</th>
          <th class="p-3 text-left">Availability</th>
          <th class="p-3 text-right">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y">
        @forelse ($products as $p)
          {{-- Data row --}}
          <tr class="hover:bg-neutral-50">
            <td class="p-3">
              <input type="checkbox" value="{{ $p->id }}" wire:model="selected">
            </td>
            <td class="p-3">
              @php
                $src = $p->image
                  ? (Str::startsWith($p->image, ['http://','https://']) ? $p->image : asset($p->image))
                  : asset('img/placeholder.png');
              @endphp
              <img src="{{ $src }}" alt="img" class="h-10 w-10 rounded object-cover border border-neutral-200">
            </td>
            <td class="p-3 font-medium">{{ $p->name }}</td>
            <td class="p-3">{{ $p->category->name ?? '—' }}</td>
            <td class="p-3">Rs {{ number_format((float)$p->price, 2) }}</td>
            <td class="p-3">
              @switch($p->status)
                @case('active')
                  <span class="inline-flex items-center rounded-full bg-green-100 text-green-700 px-2 py-0.5 text-xs">Available</span>
                  @break
                @case('inactive')
                  <span class="inline-flex items-center rounded-full bg-neutral-200 text-neutral-700 px-2 py-0.5 text-xs">Unavailable</span>
                  @break
                @default
                  <span class="inline-flex items-center rounded-full bg-amber-100 text-amber-700 px-2 py-0.5 text-xs">Archived</span>
              @endswitch
            </td>
            <td class="p-3 text-right space-x-2">
              @if ($editingId === $p->id)
                <button wire:click="save"
                  class="rounded-xl px-3 py-1.5 bg-[#6B4F3A] text-white hover:bg-[#5A3F2D]">Save</button>
                <button wire:click="cancelEdit"
                  class="rounded-xl px-3 py-1.5 border border-neutral-300 hover:bg-neutral-100">Cancel</button>
              @else
                <button wire:click="edit({{ $p->id }})"
                  class="rounded-xl px-3 py-1.5 border border-neutral-300 hover:bg-neutral-100">Edit</button>
                <button wire:click="delete({{ $p->id }})"
                  class="rounded-xl px-3 py-1.5 bg-red-600 text-white hover:bg-red-500">Remove</button>
              @endif
            </td>
          </tr>

          {{-- Inline edit row --}}
          @if ($editingId === $p->id)
            <tr class="bg-neutral-50">
              <td colspan="7" class="p-4">
                <div class="grid md:grid-cols-6 gap-3">
                  <div class="md:col-span-2">
                    <label class="block text-xs text-neutral-600">Category</label>
                    <select wire:model.defer="form.category_id"
                      class="mt-1 w-full rounded-xl border border-neutral-300 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#6B4F3A]">
                      @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                      @endforeach
                    </select>
                    @error('form.category_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                  </div>

                  <div class="md:col-span-2">
                    <label class="block text-xs text-neutral-600">Name</label>
                    <input type="text" wire:model.defer="form.name"
                      class="mt-1 w-full rounded-xl border border-neutral-300 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#6B4F3A]">
                    @error('form.name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                  </div>

                  <div>
                    <label class="block text-xs text-neutral-600">Price</label>
                    <input type="number" step="0.01" wire:model.defer="form.price"
                      class="mt-1 w-full rounded-xl border border-neutral-300 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#6B4F3A]">
                    @error('form.price') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                  </div>

                  <div>
                    <label class="block text-xs text-neutral-600">Availability</label>
                    <select wire:model.defer="form.status"
                      class="mt-1 w-full rounded-xl border border-neutral-300 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#6B4F3A]">
                      <option value="active">Available</option>
                      <option value="inactive">Unavailable</option>
                      <option value="archived">Archived</option>
                    </select>
                    @error('form.status') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                  </div>

                  <div class="md:col-span-3">
                    <label class="block text-xs text-neutral-600">Image link (URL or path)</label>
                    <input type="text" wire:model.defer="form.image"
                      class="mt-1 w-full rounded-xl border border-neutral-300 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#6B4F3A]"
                      placeholder="e.g. https://... or images/espresso.jpg">
                    @error('form.image') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                  </div>

                  <div class="md:col-span-6">
                    <label class="block text-xs text-neutral-600">Description (optional)</label>
                    <textarea wire:model.defer="form.description" rows="3"
                      class="mt-1 w-full rounded-xl border border-neutral-300 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#6B4F3A]"></textarea>
                    @error('form.description') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                  </div>
                </div>
              </td>
            </tr>
          @endif
        @empty
          <tr>
            <td colspan="7" class="p-6 text-center text-neutral-500">No products found.</td>
          </tr>
        @endforelse
      </tbody>
    </table>

    {{-- Spacer to keep height even if few rows --}}
    <div class="flex-1"></div>
  </div>

  <div>
    {{ $products->links() }}
  </div>

  {{-- CREATE MODAL --}}
  @if ($showCreate)
    <div class="fixed inset-0 bg-black/50 z-40"></div>
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="w-full max-w-2xl bg-white rounded-2xl shadow-xl p-6">
        <h3 class="text-lg font-semibold mb-4">Add product</h3>

        <form wire:submit.prevent="saveCreate" class="space-y-4">
          <div class="grid md:grid-cols-2 gap-3">
            <div>
              <label class="block text-xs text-neutral-600">Category</label>
              <select wire:model.defer="create.category_id"
                class="mt-1 w-full rounded-xl border border-neutral-300 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#6B4F3A]">
                <option value="">Select…</option>
                @foreach ($categories as $cat)
                  <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
              </select>
              @error('create.category_id') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
              <label class="block text-xs text-neutral-600">Name</label>
              <input type="text" wire:model.defer="create.name"
                class="mt-1 w-full rounded-xl border border-neutral-300 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#6B4F3A]">
              @error('create.name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
              <label class="block text-xs text-neutral-600">Price</label>
              <input type="number" step="0.01" wire:model.defer="create.price"
                class="mt-1 w-full rounded-xl border border-neutral-300 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#6B4F3A]">
              @error('create.price') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
              <label class="block text-xs text-neutral-600">Availability</label>
              <select wire:model.defer="create.status"
                class="mt-1 w-full rounded-xl border border-neutral-300 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#6B4F3A]">
                <option value="active">Available</option>
                <option value="inactive">Unavailable</option>
                <option value="archived">Archived</option>
              </select>
              @error('create.status') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-2">
              <label class="block text-xs text-neutral-600">Image link (URL or path)</label>
              <input type="text" wire:model.defer="create.image"
                class="mt-1 w-full rounded-xl border border-neutral-300 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#6B4F3A]"
                placeholder="e.g. https://... or images/latte.jpg">
              @error('create.image') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
          </div>

          <div>
            <label class="block text-xs text-neutral-600">Description (optional)</label>
            <textarea wire:model.defer="create.description" rows="3"
              class="mt-1 w-full rounded-xl border border-neutral-300 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#6B4F3A]"></textarea>
            @error('create.description') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>

          <div class="flex justify-end gap-2 pt-2">
            <button type="button" wire:click="closeCreate"
              class="rounded-xl px-4 py-2.5 border border-neutral-300 hover:bg-neutral-100">Cancel</button>
            <button type="submit"
              class="rounded-xl px-4 py-2.5 bg-[#6B4F3A] text-white hover:bg-[#5A3F2D]">Create</button>
          </div>
        </form>
      </div>
    </div>
  @endif

</div>
