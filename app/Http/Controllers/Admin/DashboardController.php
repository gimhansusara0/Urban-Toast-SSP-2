<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Summary metrics
        $totalCustomers = User::count();
        $totalOrders    = Order::count();
        $totalRevenue   = Order::where('status','paid')->sum('total');

        // Top 3 products
        $topProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as qty'))
            ->groupBy('product_id')
            ->orderByDesc('qty')
            ->with('product')
            ->take(3)
            ->get();

        // Top 3 categories
        $topCategories = Category::select('categories.id','categories.name', DB::raw('SUM(order_items.quantity) as qty'))
            ->join('products','products.category_id','=','categories.id')
            ->join('order_items','order_items.product_id','=','products.id')
            ->groupBy('categories.id','categories.name')
            ->orderByDesc('qty')
            ->take(3)
            ->get();

        return view('admin.dashboard', compact(
            'totalCustomers',
            'totalOrders',
            'totalRevenue',
            'topProducts',
            'topCategories'
        ));
    }
}
