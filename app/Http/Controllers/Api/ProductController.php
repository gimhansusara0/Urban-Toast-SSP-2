<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // GET /api/products
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);

        $q = Product::query()->with('category:id,name,slug');

        if ($request->filled('category_id')) {
            $q->where('category_id', $request->query('category_id'));
        }
        if ($request->filled('status')) {
            $q->where('status', $request->query('status'));
        }
        if ($request->filled('min_price')) {
            $q->where('price', '>=', (float) $request->query('min_price'));
        }
        if ($request->filled('max_price')) {
            $q->where('price', '<=', (float) $request->query('max_price'));
        }
        if ($request->filled('search')) {
            $s = $request->query('search');
            $q->where(function ($qq) use ($s) {
                $qq->where('name', 'like', "%{$s}%")
                   ->orWhere('description', 'like', "%{$s}%");
            });
        }

        return ProductResource::collection(
            $q->orderByDesc('id')->paginate($perPage)
        );
    }

    // POST /api/products
    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => ['required','exists:categories,id'],
            'name'        => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'price'       => ['required','numeric','min:0'],
            'stock'       => ['required','integer','min:0'],
            'status'      => ['nullable','in:active,inactive,archived'],
            'image'       => ['nullable','string','max:1024'],
        ]);

        $product = Product::create([
            'category_id' => $data['category_id'],
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'price'       => $data['price'],
            'stock'       => $data['stock'],
            'status'      => $data['status'] ?? 'active',
            'image'       => $data['image'] ?? null,
        ])->load('category:id,name,slug');

        return (new ProductResource($product))
            ->response()
            ->setStatusCode(201);
    }

    // GET /api/products/{product}
    public function show(Product $product)
    {
        return new ProductResource($product->load('category:id,name,slug'));
    }

    // PUT/PATCH /api/products/{product}
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'category_id' => ['sometimes','required','exists:categories,id'],
            'name'        => ['sometimes','required','string','max:255'],
            'description' => ['sometimes','nullable','string'],
            'price'       => ['sometimes','required','numeric','min:0'],
            'stock'       => ['sometimes','required','integer','min:0'],
            'status'      => ['sometimes','required','in:active,inactive,archived'],
            'image'       => ['sometimes','nullable','string','max:1024'],
        ]);

        $product->update($data);

        return new ProductResource($product->load('category:id,name,slug'));
    }

    // DELETE /api/products/{product}
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(['deleted' => true]);
    }
}
