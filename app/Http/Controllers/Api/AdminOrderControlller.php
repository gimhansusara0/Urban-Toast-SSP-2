<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    // GET /api/v1/admin/orders?search=John&page=1
    public function index(Request $request)
    {
        $query = Order::with(['items.product', 'user']); // eager load user & items

        if ($search = $request->query('search')) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $orders = $query->latest()->paginate(10);

        return OrderResource::collection($orders);
    }

    // GET /api/v1/admin/orders/{order}
    public function show(Order $order)
    {
        return new OrderResource($order->load(['items.product', 'user']));
    }

    // PUT /api/v1/admin/orders/{order}/status
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,complete,cancelled',
        ]);

        $order->update(['status' => $request->status]);

        return new OrderResource($order->fresh()->load(['items.product', 'user']));
    }

    // DELETE /api/v1/admin/orders/{order}
    public function destroy(Order $order)
    {
        $order->delete();

        return response()->json(['message' => 'Order deleted']);
    }
}
