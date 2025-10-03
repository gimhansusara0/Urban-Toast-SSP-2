<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        // Total customers
        $totalCustomers = User::count();

        // Total orders
        $totalOrders = Order::count();

        // Total revenue (only paid orders)
        $totalRevenue = Order::where('status', 'paid')->sum('total');

        // Top 3 products by quantity sold
        $topProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as qty'))
            ->groupBy('product_id')
            ->orderByDesc('qty')
            ->with('product')
            ->take(3)
            ->get();

        // Top 3 categories by quantity sold
        $topCategories = Category::select('categories.id','categories.name', DB::raw('SUM(order_items.quantity) as qty'))
            ->join('products','products.category_id','=','categories.id')
            ->join('order_items','order_items.product_id','=','products.id')
            ->groupBy('categories.id','categories.name')
            ->orderByDesc('qty')
            ->take(3)
            ->get();

        return view('admin.analytics.index', compact(
            'totalCustomers',
            'totalOrders',
            'totalRevenue',
            'topProducts',
            'topCategories'
        ));
    }
}
