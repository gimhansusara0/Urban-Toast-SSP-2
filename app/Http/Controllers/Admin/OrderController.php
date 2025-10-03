<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $orders = Order::with(['user', 'items.product'])
            ->when($search, function ($q) use ($search) {
                $q->whereHas('user', function ($u) use ($search) {
                    $u->where('name', 'like', '%' . $search . '%');
                });
            })
            ->latest()
            ->paginate(10);

        return view('admin.orders.index', compact('orders', 'search'));
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $order = Order::findOrFail($id);
            $order->items()->delete();
            $order->delete();
        });

        return redirect()->route('admin.orders.index')
            ->with('ok', 'Order deleted successfully.');
    }

    public function changeStatus($id, $status)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => $status]);

        return redirect()->route('admin.orders.index')
            ->with('ok', "Order #{$id} marked as {$status}.");
    }
}
