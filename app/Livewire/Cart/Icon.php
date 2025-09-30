<?php

namespace App\Livewire\Cart;

use Livewire\Component;
use App\Models\OrderItem;

class Icon extends Component
{
    public int $count = 0;

    public function mount(): void
    {
        $this->refreshCount();
    }

    protected function getListeners(): array
    {
        return ['cart-updated' => 'refreshCount'];
    }

    public function refreshCount(): void
    {
        $this->count = auth()->check()
            ? (int) OrderItem::where('user_id', auth()->id())->where('status','pending')->count()
            : 0;
    }

    public function go(): void
    {
        $this->redirectRoute('cart.index');
    }

    public function render() { return view('livewire.cart.icon'); }
}
