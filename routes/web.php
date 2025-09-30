<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\AuthController as AdminAuth;

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

