<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class Panel extends Component
{
    public string $tab = 'home'; // home|customers|orders|products

    // Analytics data
    public int $totalCustomers = 0;
    public int $totalOrders = 0;
    public float $totalRevenue = 0;
    public $topProducts = [];
    public $topCategories = [];

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
    }

    public function loadAnalytics(): void
    {
        // Customers
        $this->totalCustomers = Customer::count();

        // Orders
        $this->totalOrders = Order::count();

        // Revenue (paid only)
        $this->totalRevenue = (float) Order::where('status', 'paid')->sum('total');

        // Top products
        $this->topProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as qty'))
            ->groupBy('product_id')
            ->orderByDesc('qty')
            ->with('product')
            ->take(3)
            ->get();

        // Top categories
        $this->topCategories = Category::select('categories.id','categories.name', DB::raw('SUM(order_items.quantity) as qty'))
            ->join('products','products.category_id','=','categories.id')
            ->join('order_items','order_items.product_id','=','products.id')
            ->groupBy('categories.id','categories.name')
            ->orderByDesc('qty')
            ->take(3)
            ->get();
    }

    public function render()
    {
        // Load analytics only when Home tab is active
        if ($this->tab === 'home') {
            $this->loadAnalytics();
        }

        return view('livewire.admin.panel');
    }
}
