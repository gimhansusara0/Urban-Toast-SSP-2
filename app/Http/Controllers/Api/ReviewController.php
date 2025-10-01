<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Models\Customer;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ReviewController extends Controller
{
  
    // get the current authenticated customer's record 
    
   protected function currentCustomer(Request $request): ?Customer
{
    return $request->user(); // Customer 
}


    //  GET /api/v1/me/customer
  
    public function meCustomer(Request $request)
{
    $cust = $this->currentCustomer($request);

    return response()->json([
        'customer_id' => $cust?->id,
        'name'        => $cust?->name,
        'user_id'     => $cust?->id, // same as customer_id here
    ]);
}


    //  GET /api/v1/reviews
    //  show all general reviews
 
    public function index(Request $request)
    {
        $q = Review::with('customer')->orderByDesc('created_at');
        $q->whereNull('product_id'); // only general reviews

        return ReviewResource::collection($q->paginate((int) $request->query('per_page', 15)));
    }

    
    // GET /api/v1/reviews/my
    //  Authenticated: only my reviews
 
    public function my(Request $request)
    {
        $cust = $this->currentCustomer($request);
        if (!$cust) {
            return ReviewResource::collection(collect([])); // empty
        }

        $q = Review::with('customer')
            ->where('customer_id', $cust->id)
            ->whereNull('product_id')
            ->orderByDesc('created_at');

        return ReviewResource::collection($q->paginate((int) $request->query('per_page', 15)));
    }

  
    //   POST /api/v1/reviews
 
    public function store(Request $request)
    {
        $cust = $this->currentCustomer($request);

        $input = $request->all();
        $input['product_id'] = null; 

        $validator = Validator::make($input, [
            'rating'   => 'required|integer|min:1|max:5',
            'title'    => 'nullable|string|max:255',
            'body'     => 'nullable|string|max:2000',
            'approved' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Submit error',
                'errors'  => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $data['customer_id'] = $cust?->id;

        $review = Review::create($data);

        return (new ReviewResource($review->load('customer')))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

   
    //  GET /api/v1/reviews/{review}
   
    public function show(Review $review)
    {
        return new ReviewResource($review->load('customer'));
    }

 
    //  PUT /api/v1/reviews/{review}
  
    public function update(Request $request, Review $review)
    {
        $cust = $this->currentCustomer($request);
        if (!$cust || $review->customer_id !== $cust->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'rating'   => 'sometimes|integer|min:1|max:5',
            'title'    => 'nullable|string|max:255',
            'body'     => 'nullable|string|max:2000',
            'approved' => 'sometimes|boolean',
        ]);

        $review->update($data);

        return new ReviewResource($review->refresh()->load('customer'));
    }

   
    //   DELETE /api/v1/reviews/{review}

    public function destroy(Request $request, Review $review)
    {
        $cust = $this->currentCustomer($request);
        if (!$cust || $review->customer_id !== $cust->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $review->delete();
        return response()->json(['message' => 'Review deleted']);
    }


    //  product rating

    public function productRating($productId)
    {
        $stats = Review::where('product_id', $productId)
            ->where('approved', true)
            ->selectRaw('COUNT(*) as count, AVG(rating) as avg_rating')
            ->first();

        return response()->json([
            'product_id' => (int) $productId,
            'count'      => (int) ($stats->count ?? 0),
            'avg_rating' => $stats->avg_rating ? round((float) $stats->avg_rating, 2) : 0,
        ]);
    }
}
