<?php

namespace App\Livewire\Cart;

use Livewire\Component;
use Illuminate\Support\Facades\Http;

class CartPage extends Component
{
    public array $items = [];
    public float $total = 0;

    public function mount(): void
    {
        $this->loadItems();
    }

    public function loadItems(): void
    {
        $response = Http::withOptions(['with_credentials' => true])
            ->get(url('/api/v1/cart'));

        if ($response->successful()) {
            $this->items = $response->json('data') ?? [];
            $this->total = collect($this->items)
                ->where('available', true)
                ->sum(fn($i) => $i['price_each'] * $i['quantity']);
        } else {
            $this->items = [];
            $this->total = 0;
        }
    }

    public function inc(int $itemId): void
    {
        $this->updateQuantity($itemId, fn($qty) => min(20, $qty + 1));
    }

    public function dec(int $itemId): void
    {
        $this->updateQuantity($itemId, fn($qty) => max(1, $qty - 1));
    }

    protected function updateQuantity(int $itemId, \Closure $adjust): void
    {
        $item = collect($this->items)->firstWhere('id', $itemId);
        if (!$item) return;

        $newQty = $adjust($item['quantity']);

        Http::withOptions(['with_credentials' => true])
            ->put(url("/api/v1/cart/{$itemId}"), [
                'quantity' => $newQty,
            ]);

        $this->dispatch('cart-updated');
        $this->loadItems();
    }

    public function remove(int $itemId): void
    {
        Http::withOptions(['with_credentials' => true])
            ->delete(url("/api/v1/cart/{$itemId}"));

        $this->dispatch('cart-updated');
        $this->loadItems();
    }

    public function checkout(): void
    {
        $response = Http::withOptions(['with_credentials' => true])
            ->post(url('/api/v1/orders/checkout'));

        if ($response->successful()) {
            $this->dispatch('cart-updated');
            $this->loadItems();
            session()->flash('ok', 'Thanks! Your order was placed.');
        } else {
            session()->flash('error', 'Checkout failed. Try again.');
        }
    }

    public function render()
    {
        return view('livewire.cart.cart-page');
    }
}
