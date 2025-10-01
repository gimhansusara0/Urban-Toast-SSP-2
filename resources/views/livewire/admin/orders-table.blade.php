<div class="bg-white rounded-2xl shadow p-6">
  <h1 class="text-xl font-semibold mb-4">Orders</h1>

  <!-- Search -->
  <div class="flex gap-3 mb-4">
    <input type="text" wire:model.debounce.500ms="search"
           placeholder="Search by customer name"
           class="flex-1 rounded-xl border border-neutral-300 px-4 py-2.5 focus:ring-2 focus:ring-[#6B4F3A]">
  </div>

  <!-- Table -->
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-neutral-50 text-neutral-600">
        <tr>
          <th class="p-3 text-left">Order ID</th>
          <th class="p-3 text-left">Customer</th>
          <th class="p-3 text-left">Total</th>
          <th class="p-3 text-left">Status</th>
          <th class="p-3 text-left">Purchased At</th>
          <th class="p-3 text-right">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y">
        @forelse ($orders as $order)
          <tr>
            <td class="p-3">{{ $order['id'] }}</td>
            <td class="p-3">{{ $order['user']['name'] ?? '-' }}</td>
            <td class="p-3">Rs {{ $order['total'] }}</td>
            <td class="p-3">
              <select wire:change="$emit('updateStatus', {{ $order['id'] }}, $event.target.value)"
                      class="rounded border px-2 py-1">
                <option value="pending" @selected($order['status']==='pending')>Pending</option>
                <option value="paid" @selected($order['status']==='paid')>Paid</option>
                <option value="completed" @selected($order['status']==='completed')>Completed</option>
                <option value="cancelled" @selected($order['status']==='cancelled')>Cancelled</option>
              </select>
            </td>
            <td class="p-3">{{ $order['purchased_at'] ?? '-' }}</td>
            <td class="p-3 text-right">
              <button wire:click="$emit('viewOrder', {{ $order['id'] }})"
                      class="px-3 py-1 bg-neutral-200 rounded">View</button>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="p-3 text-center text-neutral-500">No orders found</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <div class="flex gap-2 mt-4">
    @if ($pagination)
      @for ($i = 1; $i <= $pagination['last_page']; $i++)
        <button wire:click="gotoPage({{ $i }})"
                class="px-3 py-1 rounded {{ $i === $pagination['current_page'] ? 'bg-[#6B4F3A] text-white' : 'bg-neutral-200' }}">
          {{ $i }}
        </button>
      @endfor
    @endif
  </div>
</div>
