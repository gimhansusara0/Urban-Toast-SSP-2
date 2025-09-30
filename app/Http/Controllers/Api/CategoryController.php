<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // GET /api/categories
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 15);

        $q = Category::query();

        if ($request->boolean('with_counts')) {
            $q->withCount('products');
        }

        if ($request->filled('status')) {
            $q->where('status', $request->query('status'));
        }
        if ($request->filled('search')) {
            $s = $request->query('search');
            $q->where(function ($qq) use ($s) {
                $qq->where('name', 'like', "%{$s}%")
                   ->orWhere('slug', 'like', "%{$s}%");
            });
        }

        return CategoryResource::collection(
            $q->orderByDesc('id')->paginate($perPage)
        );
    }

    // POST /api/categories
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'   => ['required','string','max:255'],
            'slug'   => ['nullable','string','max:255','unique:categories,slug'],
            'status' => ['nullable','in:active,inactive'],
        ]);

        $cat = Category::create([
            'name'   => $data['name'],
            'slug'   => $data['slug'] ?? null, // model will auto-generate if null/empty
            'status' => $data['status'] ?? 'active',
        ]);

        return (new CategoryResource($cat))
            ->response()
            ->setStatusCode(201);
    }

    // GET /api/categories/{category}
    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    // PUT/PATCH /api/categories/{category}
    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name'   => ['sometimes','required','string','max:255'],
            'slug'   => ['sometimes','nullable','string','max:255','unique:categories,slug,' . $category->id],
            'status' => ['sometimes','required','in:active,inactive'],
        ]);

        $category->update($data);

        return new CategoryResource($category);
    }

    // DELETE /api/categories/{category}
    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json(['deleted' => true]);
    }
}
