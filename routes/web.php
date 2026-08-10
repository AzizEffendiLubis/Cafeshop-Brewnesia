<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;

// ──────────────────────────────────────────
// Public / Auth Routes
// ──────────────────────────────────────────
Route::get('/', fn() => redirect()->route('guest.select'));
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ──────────────────────────────────────────
// Guest / Customer Routes (no account needed)
// ──────────────────────────────────────────
Route::get('/pilih-meja', [AuthController::class, 'guestSelect'])->name('guest.select');
Route::post('/pilih-meja', [AuthController::class, 'guestLogin'])->name('guest.login');

Route::middleware('guest.table')->prefix('order')->name('customer.')->group(function () {
    Route::get('/menu', [CustomerController::class, 'menu'])->name('menu');
    Route::get('/checkout', [CustomerController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [CustomerController::class, 'placeOrder'])->name('place-order');
    Route::get('/my-orders', [CustomerController::class, 'orders'])->name('orders');
    Route::get('/order-status/{order}', [CustomerController::class, 'orderStatus'])->name('order-status');
    Route::get('/generate-qr', [CustomerController::class, 'generateQR'])->name('generate-qr');
    // Cart API
    Route::post('/cart/add', [CustomerController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/update', [CustomerController::class, 'updateCart'])->name('cart.update');
    Route::get('/cart', [CustomerController::class, 'getCart'])->name('cart.get');
});

// ──────────────────────────────────────────
// Admin Routes (auth + admin middleware)
// ──────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [OrderController::class, 'dashboard'])->name('dashboard');

    // Menu CRUD
    Route::resource('menus', MenuController::class);
    Route::patch('/menus/{menu}/toggle', [MenuController::class, 'toggleAvailability'])->name('menus.toggle');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
    Route::get('/live-orders', [OrderController::class, 'liveOrders'])->name('live-orders');
});
