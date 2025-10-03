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

    // Cancel an order (only if still pending or paid)
    public function cancel(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized');
        }

        if (! in_array($order->status, ['pending', 'paid'])) {
            return response()->json(['message' => 'Order cannot be cancelled.'], 400);
        }

        DB::transaction(function () use ($order) {
            $order->update(['status' => 'cancelled']);

            $order->items()->update(['status' => 'cancelled']);
        });

        return new OrderResource($order->fresh()->load('items.product'));
    }

    //Reorder (duplicate previous order into a new pending cart)
    public function reorder(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized');
        }

        $newItems = [];
        foreach ($order->items as $item) {
            $newItems[] = OrderItem::create([
                'user_id'   => $request->user()->id,
                'product_id'=> $item->product_id,
                'quantity'  => $item->quantity,
                'line_total'=> $item->line_total,
                'status'    => 'pending',
            ]);
        }

        return response()->json([
            'message' => 'Items added back to your cart.',
            'items'   => $newItems,
        ]);
    }

    //  Admin can update status
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,completed,cancelled',
        ]);

        $order->update(['status' => $request->status]);

        return new OrderResource($order->fresh()->load('items.product'));
    }
}
