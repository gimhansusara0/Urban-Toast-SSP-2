<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // GET /api/orders
    public function index(Request $request)
    {
        $orders = Order::with('items.product')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return OrderResource::collection($orders);
    }

    // POST /api/orders/checkout
    public function checkout(Request $request)
    {
        $userId = $request->user()->id;

        return DB::transaction(function () use ($userId) {
            $items = OrderItem::with('product')
                ->where('user_id', $userId)
                ->where('status', 'pending')
                ->get();

            $purchasable = $items->filter(fn($i) => $i->product?->status === 'active');

            if ($purchasable->isEmpty()) {
                return response()->json(['message' => 'Cart is empty or no available items'], 400);
            }

            $total = $purchasable->sum(fn($i) => $i->line_total);

            $order = Order::create([
                'user_id'      => $userId,
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

            return new OrderResource($order->load('items.product'));
        });
    }

    // GET /api/orders/{order}
    public function show(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized');
        }

        return new OrderResource($order->load('items.product'));
    }
}
