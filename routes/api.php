<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;

Route::middleware('auth:sanctum')->prefix('v1')->as('api.')->group(function () {
    // User info
    Route::get('/user', fn (Request $r) => $r->user());

    // Categories & Products
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('products', ProductController::class);

    // Cart
    // Cart
    Route::get('cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('cart', [CartController::class, 'store'])->name('cart.store');
    Route::put('cart/{item}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('cart/{item}', [CartController::class, 'destroy'])->name('cart.destroy');


    // Orders
    Route::get('orders', [OrderController::class, 'index']);
    Route::get('orders/{order}', [OrderController::class, 'show']);
    Route::post('orders/checkout', [OrderController::class, 'checkout']);
});
