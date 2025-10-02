<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\AdminOrderController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\ReviewController;


// Public endpoints


// Public API login
Route::post('/login', [AuthController::class, 'login']);

// Public reviews (read only)
Route::prefix('v1')->as('api.')->group(function () {
    Route::get('reviews', [ReviewController::class, 'index']);   // all reviews
    
});

Route::prefix('v1')->group(function () {
    Route::get('reservations', [ReservationController::class, 'index']);
    Route::post('reservations', [ReservationController::class, 'store']);
    Route::put('reservations/{id}', [ReservationController::class, 'update']);
});


// Protected endpoints, needs the sanctum auth

Route::middleware('auth:sanctum')->prefix('v1')->as('api.')->group(function () {
    // Authenticated user info
    Route::get('/user', fn (Request $r) => $r->user());
    Route::get('/me/customer', [ReviewController::class, 'meCustomer']);

    // My reviews 
    Route::get('reviews/my', [ReviewController::class, 'my']);

    // Reviews (create, update, delete, show single)
    Route::post('reviews', [ReviewController::class, 'store']);
    Route::get('reviews/{review}', [ReviewController::class, 'show']); 
    Route::put('reviews/{review}', [ReviewController::class, 'update']);
    Route::delete('reviews/{review}', [ReviewController::class, 'destroy']);

    // Categories & Products
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('products', ProductController::class);

    // Cart
    Route::get('cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('cart', [CartController::class, 'store'])->name('cart.store');
    Route::put('cart/{item}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('cart/{item}', [CartController::class, 'destroy'])->name('cart.destroy');

    // Orders
    Route::get('orders', [OrderController::class, 'index']);
    Route::get('orders/{order}', [OrderController::class, 'show']);
    Route::post('orders/checkout', [OrderController::class, 'checkout']);

    // Product rating
    Route::get('products/{product}/rating', [ReviewController::class, 'productRating']);
});


// Admin APIs

Route::middleware('auth:admin')->prefix('v1/admin')->as('api.admin.')->group(function () {
    Route::get('orders', [AdminOrderController::class, 'index']);
    Route::get('orders/{order}', [AdminOrderController::class, 'show']);
    Route::put('orders/{order}/status', [AdminOrderController::class, 'updateStatus']);
    Route::delete('orders/{order}', [AdminOrderController::class, 'destroy']);
});
