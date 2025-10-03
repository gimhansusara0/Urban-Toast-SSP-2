@extends('admin.layout')

@section('content')
<div class="space-y-4">

  {{-- Header / Tools --}}
  <div class="bg-white rounded-2xl shadow p-4 flex flex-col gap-4">
    
    {{-- Back to Dashboard --}}
    <div>
      <a href="{{ route('admin.dashboard') }}"
         class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 bg-neutral-200 text-neutral-700 hover:bg-neutral-300 transition">
        ← Back to Dashboard
      </a>
    </div>

    {{-- Search form --}}
    <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-col md:flex-row md:items-center gap-3">
      <div class="flex-1">
        <input
          type="text"
          name="search"
          value="{{ $search }}"
          placeholder="Search orders by customer name…"
          class="w-full rounded-xl border border-neutral-300 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#6B4F3A]">
      </div>
      <button type="submit" class="rounded-xl px-4 py-2.5 bg-blue-500 text-white hover:bg-blue-600">
        Search
      </button>
    </form>

    @if (session('ok'))
      <div class="mt-3 text-sm text-green-700 bg-green-50 border border-green-200 rounded-lg p-3">
        {{ session('ok') }}
      </div>
    @endif
  </div>

  {{-- Table --}}
  <div class="bg-white rounded-2xl shadow overflow-hidden min-h-[520px] flex flex-col">
    <table class="w-full text-sm">
      <thead class="bg-neutral-50 text-neutral-600">
        <tr>
          <th class="p-3 text-left">ID</th>
          <th class="p-3 text-left">Customer</th>
          <th class="p-3 text-left">Total</th>
          <th class="p-3 text-left">Status</th>
          <th class="p-3 text-left">Date</th>
          <th class="p-3 text-right">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y">
        @forelse ($orders as $order)
          <tr class="hover:bg-neutral-50">
            <td class="p-3 font-medium">#{{ $order->id }}</td>
            <td class="p-3">{{ $order->user->name ?? '—' }}</td>
            <td class="p-3">Rs {{ number_format((float)$order->total, 2) }}</td>
            <td class="p-3">
              @switch($order->status)
                @case('pending')
                  <span class="inline-flex items-center rounded-full bg-amber-100 text-amber-700 px-2 py-0.5 text-xs">Pending</span>
                  @break
                @case('paid')
                  <span class="inline-flex items-center rounded-full bg-green-100 text-green-700 px-2 py-0.5 text-xs">Paid</span>
                  @break
                @case('completed')
                  <span class="inline-flex items-center rounded-full bg-blue-100 text-blue-700 px-2 py-0.5 text-xs">Completed</span>
                  @break
                @default
                  <span class="inline-flex items-center rounded-full bg-neutral-200 text-neutral-700 px-2 py-0.5 text-xs">{{ ucfirst($order->status) }}</span>
              @endswitch
            </td>
            <td class="p-3">{{ $order->purchased_at?->format('Y-m-d H:i') }}</td>
            <td class="p-3 text-right space-x-2">
              {{-- Status change form (PATCH) --}}
              <form method="POST" 
                    action="{{ route('admin.orders.changeStatus', [$order->id, $order->status === 'completed' ? 'pending' : 'completed']) }}" 
                    class="inline">
                @csrf
                @method('PATCH')
                <button type="submit" 
                  class="rounded-xl px-3 py-1.5 bg-[#6B4F3A] text-white hover:bg-[#5A3F2D]">
                  Mark {{ $order->status === 'completed' ? 'Pending' : 'Complete' }}
                </button>
              </form>

              {{-- Delete order --}}
              <form method="POST" action="{{ route('admin.orders.destroy', $order->id) }}" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm('Delete this order?')"
                  class="rounded-xl px-3 py-1.5 bg-red-600 text-white hover:bg-red-500">
                  Remove
                </button>
              </form>
            </td>
          </tr>
          <tr class="bg-neutral-50">
            <td colspan="6" class="p-4">
              <h4 class="font-semibold mb-2">Products in this order:</h4>
              <ul class="space-y-1">
                @foreach ($order->items as $item)
                  <li class="flex justify-between text-sm">
                    <span>{{ $item->product->name ?? '—' }} × {{ $item->quantity }}</span>
                    <span>Rs {{ number_format($item->line_total, 2) }}</span>
                  </li>
                @endforeach
              </ul>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="p-6 text-center text-neutral-500">No orders found.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div>{{ $orders->links() }}</div>
</div>
@endsection
