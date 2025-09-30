<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderItemResource;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // GET /api/cart
    public function index(Request $request)
    {
        $items = OrderItem::with('product')
            ->where('user_id', $request->user()->id)
            ->where('status', 'pending')
            ->get();

        return OrderItemResource::collection($items);
    }

    // POST /api/cart
    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required','exists:products,id'],
            'quantity'   => ['nullable','integer','min:1','max:20'],
        ]);

        $product = Product::where('status','active')->findOrFail($data['product_id']);

        $item = OrderItem::firstOrCreate(
            [
                'user_id'    => $request->user()->id,
                'product_id' => $product->id,
                'status'     => 'pending',
            ],
            [
                'quantity'   => $data['quantity'] ?? 1,
                'price_each' => $product->price,
            ]
        );

        return new OrderItemResource($item->load('product'));
    }

    // PUT /api/cart/{id}
    public function update(Request $request, OrderItem $item)
    {
        $this->authorizeItem($item, $request);

        $data = $request->validate([
            'quantity' => ['required','integer','min:1','max:20'],
        ]);

        $item->update(['quantity' => $data['quantity']]);

        return new OrderItemResource($item->load('product'));
    }

    // DELETE /api/cart/{id}
    public function destroy(Request $request, OrderItem $item)
    {
        $this->authorizeItem($item, $request);

        $item->delete();

        return response()->json(['deleted' => true]);
    }

    protected function authorizeItem(OrderItem $item, Request $request): void
    {
        if ($item->user_id !== $request->user()->id || $item->status !== 'pending') {
            abort(403, 'Unauthorized');
        }
    }
}
