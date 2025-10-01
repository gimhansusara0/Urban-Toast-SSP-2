<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\AuthController as AdminAuth;
use App\Http\Controllers\Api\ReviewController;

/**
 * PUBLIC HOME (customers)
 * resources/views/customers/home.blade.php
 */
Route::get('/', fn () => view('customers.home'))->name('home');

/**
 * Customer routes (Jetstream)
 */
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');
});

/**
 * Admin routes
 */
Route::prefix('admin')->group(function () {
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminAuth::class, 'showLogin'])->name('admin.login');
        Route::post('/login', [AdminAuth::class, 'login'])->name('admin.login.post');
    });

    Route::middleware('auth:admin')->group(function () {
        Route::view('/dashboard', 'admin.dashboard')->name('admin.dashboard');
        Route::post('/logout', [AdminAuth::class, 'logout'])->name('admin.logout');
    });

    Route::redirect('/', '/admin/dashboard');
});

/**
 * Role picker (guest users go here to pick Admin/Customer auth)
 */
Route::get('/auth/role', function () {
    if (Auth::guard('admin')->check()) {
        return redirect()->route('admin.dashboard');
    }
    if (Auth::guard('web')->check()) { // customer
        return redirect()->route('home');
    }
    return view('auth.role-pick');
})->name('auth.role');


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

    // Cart page
    Route::get('/cart', function () {
        return view('customers.cart'); // simple wrapper view
    })->name('cart.index');
});

Route::view('/reviews', 'reviews.index')->name('reviews.page');


// Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
// Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
// Route::get('/reviews/{review}', [ReviewController::class, 'show'])->name('reviews.show');
// Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
// Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

// Route::get('/products/{product}/rating', [ReviewController::class, 'productRating'])->name('products.rating');