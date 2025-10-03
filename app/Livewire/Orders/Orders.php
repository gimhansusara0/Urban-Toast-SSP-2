<?php

namespace App\Http\Livewire\Orders;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class UserOrdersTable extends Component
{
    use WithPagination;

    public $searchInput = '';
    public $selected = [];
    public $editingId = null;
    public $statusUpdate = null;
    public $showProductsFor = null;

    protected $paginationTheme = 'tailwind';

    public function updatingSearchInput()
    {
        $this->resetPage();
    }

    public function toggleSelectAll()
    {
        if (count($this->selected)) {
            $this->selected = [];
        } else {
            $this->selected = Order::pluck('id')->toArray();
        }
    }

    public function delete($id)
    {
        DB::transaction(function () use ($id) {
            $order = Order::findOrFail($id);
            $order->items()->delete();
            $order->delete();
        });

        session()->flash('ok', 'Order deleted successfully.');
    }

    public function changeStatus($id, $newStatus)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => $newStatus]);
        session()->flash('ok', "Order #{$id} marked as {$newStatus}.");
    }

    public function viewProducts($id)
    {
        if ($this->showProductsFor === $id) {
            $this->showProductsFor = null;
        } else {
            $this->showProductsFor = $id;
        }
    }

    public function render()
    {
        $orders = Order::with(['user', 'items.product'])
            ->when($this->searchInput, function ($q) {
                $q->whereHas('user', function ($u) {
                    $u->where('name', 'like', '%' . $this->searchInput . '%');
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.Orders.new-orders', [
            'orders' => $orders,
        ]);
    }
}
