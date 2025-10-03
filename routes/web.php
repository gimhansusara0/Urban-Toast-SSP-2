<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\AuthController as AdminAuth;
use App\Livewire\Reservations\ReservationPage;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\DashboardController; 

//   resources/views/customers/home.blade.php
Route::get('/', fn () => view('customers.home'))->name('home');

//   Customer routes (Jetstream)
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

    // Cart page
    Route::get('/cart', function () {
        return view('customers.cart'); 
    })->name('cart.index');
});

// Reviews page
Route::view('/reviews', 'reviews.index')->name('reviews.page');

// Reservations (Jetstream's default web guard)
Route::middleware(['auth'])
    ->get('/reservations', ReservationPage::class)
    ->name('reservations.index');

//  Admin routes
Route::prefix('admin')->group(function () {

    // Admin login (guest)
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminAuth::class, 'showLogin'])->name('admin.login');
        Route::post('/login', [AdminAuth::class, 'login'])->name('admin.login.post');
    });

    // Admin protected routes
    Route::middleware('auth:admin')->group(function () {
        // Admin dashboard and DashboardController@index
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::post('/logout', [AdminAuth::class, 'logout'])->name('admin.logout');

        // Orders
        Route::get('/orders', [OrderController::class, 'index'])->name('admin.orders.index');
        Route::delete('/orders/{id}', [OrderController::class, 'destroy'])->name('admin.orders.destroy');
        Route::patch('/orders/{id}/status/{status}', [OrderController::class, 'changeStatus'])->name('admin.orders.changeStatus');
    });

    // Redirect /admin  to /admin/dashboard
    Route::redirect('/', '/admin/dashboard');
});

// Role picker, guest or admin
Route::get('/auth/role', function () {
    if (Auth::guard('admin')->check()) {
        return redirect()->route('admin.dashboard');
    }
    if (Auth::guard('web')->check()) { // customer
        return redirect()->route('home');
    }
    return view('auth.role-pick');
})->name('auth.role');

// Customer navs
Route::view('/about', 'about')->name('about');
Route::view('/contact', 'contact')->name('contact');
