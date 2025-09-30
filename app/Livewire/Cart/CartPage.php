<?php

namespace App\Livewire\Cart;

use Livewire\Component;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class CartPage extends Component
{
    public array $items = []; // hydrated list for rendering
    public float $total = 0;  // only counts available items

    public function mount(): void
    {
        $this->loadItems();
    }

    public function loadItems(): void
    {
        $rows = OrderItem::with('product')
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->latest()->get();

        $this->items = $rows->map(function ($i) {
            $available = optional($i->product)->status === 'active';
            return [
                'id'         => $i->id,
                'name'       => optional($i->product)->name ?? 'Unknown product',
                'image'      => optional($i->product)->image,
                'available'  => $available,
                'quantity'   => $i->quantity,
                'price_each' => (float) $i->price_each,
                'line'       => (float) $i->price_each * $i->quantity,
            ];
        })->toArray();

        // total only counts available items
        $this->total = collect($this->items)
            ->where('available', true)
            ->sum(fn($i) => $i['price_each'] * $i['quantity']);
    }

    public function inc(int $itemId): void
    {
        $row = OrderItem::where('user_id', auth()->id())->findOrFail($itemId);
        if ($row->product?->status !== 'active') return; // cannot change unavailable
        $row->quantity = min(20, $row->quantity + 1);
        $row->save();
        $this->dispatch('cart-updated');
        $this->loadItems();
    }

    public function dec(int $itemId): void
    {
        $row = OrderItem::where('user_id', auth()->id())->findOrFail($itemId);
        if ($row->product?->status !== 'active') return;
        $row->quantity = max(1, $row->quantity - 1);
        $row->save();
        $this->dispatch('cart-updated');
        $this->loadItems();
    }

    public function remove(int $itemId): void
    {
        OrderItem::where('user_id', auth()->id())->whereKey($itemId)->delete();
        $this->dispatch('cart-updated');
        $this->loadItems();
    }

    public function checkout(): void
    {
        // only purchase available items; ignore unavailable
        DB::transaction(function () {
            $items = OrderItem::with('product')
                ->where('user_id', auth()->id())
                ->where('status','pending')
                ->get();

            $purchasable = $items->filter(fn($i) => $i->product?->status === 'active');

            if ($purchasable->isEmpty()) return;

            $total = $purchasable->sum(fn($i) => (float)$i->price_each * $i->quantity);

            $order = Order::create([
                'user_id'      => auth()->id(),
                'status'       => 'paid',
                'total'        => $total,
                'purchased_at' => now(),
            ]);

            OrderItem::whereIn('id', $purchasable->pluck('id'))
                ->update([
                    'status'       => 'purchased',
                    'order_id'     => $order->id,
                    'purchased_at' => now(),
                ]);
        });

        $this->dispatch('cart-updated');
        $this->loadItems();
        session()->flash('ok', 'Thanks! Your order was placed.');
    }

    public function render() { return view('livewire.cart.cart-page'); }
}
