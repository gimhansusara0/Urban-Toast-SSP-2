<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $q = Review::with('customer')->orderByDesc('created_at');

        if ($productId = $request->query('product_id')) {
            $q->where('product_id', $productId);
        }

        if (!is_null($request->query('approved'))) {
            $q->where('approved', (bool)$request->query('approved'));
        }

        $perPage = (int) $request->query('per_page', 15);
        return ReviewResource::collection($q->paginate($perPage));
    }

    public function store(Request $request)
{
    // Normalize empty strings to null for customer_id
    $input = $request->all();
    if (array_key_exists('customer_id', $input) && ($input['customer_id'] === '' || is_null($input['customer_id']))) {
        $input['customer_id'] = null;
    }

    $validator = \Validator::make($input, [
        'customer_id' => 'nullable|exists:customers,id',
        'product_id'  => 'required|integer',
        'rating'      => 'required|integer|min:1|max:5',
        'title'       => 'nullable|string|max:255',
        'body'        => 'nullable|string|max:2000',
        'approved'    => 'sometimes|boolean',
    ]);

    if ($validator->fails()) {
        return response()->json(['message' => 'Submit error', 'errors' => $validator->errors()], 422);
    }

    $data = $validator->validated();

    // Prevent duplicate by same customer for same product (optional)
    if (!empty($data['customer_id'])) {
        $existing = Review::where('customer_id', $data['customer_id'])
                           ->where('product_id', $data['product_id'])
                           ->first();
        if ($existing) {
            $existing->update($data);
            return new ReviewResource($existing->load('customer'));
        }
    }

    $review = Review::create($data);
    return (new ReviewResource($review->load('customer')))->response()->setStatusCode(201);
}


    public function show(Review $review)
    {
        return new ReviewResource($review->load('customer'));
    }

    public function update(Request $request, Review $review)
    {
        $data = $request->validate([
            'rating'   => 'sometimes|integer|min:1|max:5',
            'title'    => 'nullable|string|max:255',
            'body'     => 'nullable|string|max:2000',
            'approved' => 'sometimes|boolean',
        ]);

        $review->update($data);
        return new ReviewResource($review->refresh()->load('customer'));
    }

    public function destroy(Review $review)
    {
        $review->delete();
        return response()->json(['message' => 'Review deleted']);
    }

    public function productRating($productId)
    {
        $stats = Review::where('product_id', $productId)
            ->where('approved', true)
            ->selectRaw('COUNT(*) as count, AVG(rating) as avg_rating')
            ->first();

        return response()->json([
            'product_id' => (int)$productId,
            'count'      => (int)($stats->count ?? 0),
            'avg_rating' => $stats->avg_rating ? round((float)$stats->avg_rating, 2) : 0,
        ]);
    }
}
