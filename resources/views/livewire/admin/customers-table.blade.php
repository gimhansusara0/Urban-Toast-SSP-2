<div class="space-y-4"><!-- SINGLE ROOT -->

  {{-- Header / Tools --}}
  <div class="bg-white rounded-2xl shadow p-4">
    <form wire:submit.prevent="applySearch" class="flex flex-col md:flex-row md:items-center gap-3">
      <div class="flex-1">
        <input
          type="text"
          wire:model.defer="searchInput"
          wire:keydown.enter="applySearch"
          placeholder="Search by name…"
          class="w-full rounded-xl border border-neutral-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#6B4F3A]">
      </div>

      <button type="submit" class="rounded-xl px-4 py-2.5 bg-blue-500 text-white hover:bg-blue-600">
        Search
      </button>

      <select
        wire:change="changeStatus($event.target.value)"
        class="rounded-xl border border-neutral-300 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#6B4F3A]">
        <option value="all" @selected($status==='all')>All</option>
        <option value="active" @selected($status==='active')>Active</option>
        <option value="inactive" @selected($status==='inactive')>Inactive</option>
      </select>

      

      <button type="button" wire:click="bulkDeactivate" class="rounded-xl px-4 py-2.5 border border-neutral-300 hover:bg-neutral-100">
        Update Status
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

  {{-- Table --}}
  <div class="bg-white rounded-2xl shadow overflow-hidden">
    <table class="w-full text-sm">
      <thead class="bg-neutral-50 text-neutral-600">
        <tr>
          <th class="p-3 w-10">
            <input type="checkbox" wire:click="toggleSelectAll">
          </th>
          <th class="p-3 text-left">Name</th>
          <th class="p-3 text-left">Email</th>
          <th class="p-3 text-left">Address</th>
          <th class="p-3 text-left">Status</th>
          <th class="p-3 text-right">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y">
        @forelse ($customers as $c)
          {{-- Data row --}}
          <tr class="hover:bg-neutral-50">
            <td class="p-3">
              <input type="checkbox" value="{{ $c->id }}" wire:model="selected">
            </td>
            <td class="p-3 font-medium">{{ $c->name }}</td>
            <td class="p-3">{{ $c->email }}</td>
            <td class="p-3">{{ $c->address }}</td>
            <td class="p-3">
              @if($c->status === 'active')
                <span class="inline-flex items-center rounded-full bg-green-100 text-green-700 px-2 py-0.5 text-xs">Active</span>
              @else
                <span class="inline-flex items-center rounded-full bg-neutral-200 text-neutral-700 px-2 py-0.5 text-xs">Inactive</span>
              @endif
            </td>
            <td class="p-3 text-right">
              @if ($editingId === $c->id)
                <button wire:click="save"
                  class="rounded-xl px-3 py-1.5 bg-[#6B4F3A] text-white hover:bg-[#5A3F2D]">Save</button>
                <button wire:click="cancelEdit"
                  class="ml-2 rounded-xl px-3 py-1.5 border border-neutral-300 hover:bg-neutral-100">Cancel</button>
              @else
                <button wire:click="edit({{ $c->id }})"
                  class="rounded-xl px-3 py-1.5 border border-neutral-300 hover:bg-neutral-100">Edit</button>
              @endif
            </td>
          </tr>

          {{-- Inline edit row --}}
          @if ($editingId === $c->id)
            <tr class="bg-neutral-50">
              <td colspan="6" class="p-4">
                <div class="grid md:grid-cols-4 gap-3">
                  <div>
                    <label class="block text-xs text-neutral-600">Name</label>
                    <input type="text" wire:model.defer="form.name"
                      class="mt-1 w-full rounded-xl border border-neutral-300 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#6B4F3A]">
                    @error('form.name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                  </div>
                  <div>
                    <label class="block text-xs text-neutral-600">Email</label>
                    <input type="email" wire:model.defer="form.email"
                      class="mt-1 w-full rounded-xl border border-neutral-300 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#6B4F3A]">
                    @error('form.email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                  </div>
                  <div>
                    <label class="block text-xs text-neutral-600">Address</label>
                    <input type="text" wire:model.defer="form.address"
                      class="mt-1 w-full rounded-xl border border-neutral-300 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#6B4F3A]">
                    @error('form.address') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                  </div>
                  <div>
                    <label class="block text-xs text-neutral-600">Status</label>
                    <select wire:model.defer="form.status"
                      class="mt-1 w-full rounded-xl border border-neutral-300 px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#6B4F3A]">
                      <option value="active">Active</option>
                      <option value="inactive">Inactive</option>
                    </select>
                    @error('form.status') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                  </div>
                </div>
              </td>
            </tr>
          @endif
        @empty
          <tr>
            <td colspan="6" class="p-6 text-center text-neutral-500">No customers found.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div>
    {{ $customers->links() }}
  </div>

</div>
