<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminNewsController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\OrderController;

// ═══════════════════════════════════════════════════
//  FRONTEND
// ═══════════════════════════════════════════════════

Route::get('/', [HomeController::class, 'index'])->name('home');

// Sản phẩm
Route::prefix('products')->name('products.')->group(function () {
    Route::get('/',             [ProductController::class, 'index'])->name('index');
    Route::get('/{id}',         [ProductController::class, 'detail'])->name('detail');
    Route::post('/{id}/rating', [ProductController::class, 'rating'])->name('rating');
});

// Tìm kiếm
Route::get('/search', [SearchController::class, 'index'])->name('search');

// Tin tức
Route::prefix('news')->name('news.')->group(function () {
    Route::get('/',     [NewsController::class, 'index'])->name('index');
    Route::get('/{id}', [NewsController::class, 'detail'])->name('detail');
});

// Liên hệ
Route::get('/contact', [ContactController::class, 'index'])->name('contact');

// Trang tĩnh
Route::get('/about',  [PageController::class, 'about'])->name('about');
Route::get('/policy', [PageController::class, 'policy'])->name('policy');

// Đơn hàng của khách hàng
Route::prefix('orders')->name('orders.')->middleware('customer.auth')->group(function () {
    Route::get('/',      [OrderController::class, 'index'])->name('index');
    Route::get('/{id}',  [OrderController::class, 'detail'])->name('detail');
});

// Tài khoản khách hàng
Route::prefix('account')->name('account.')->group(function () {
    Route::get('/login',     [AccountController::class, 'loginForm'])->name('login');
    Route::post('/login',    [AccountController::class, 'login'])->name('login.post');
    Route::get('/register',  [AccountController::class, 'registerForm'])->name('register');
    Route::post('/register', [AccountController::class, 'register'])->name('register.post');
    Route::post('/logout',   [AccountController::class, 'logout'])->name('logout');
});

// Giỏ hàng
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/',                    [CartController::class, 'index'])->name('index');
    Route::post('/add/{id}',           [CartController::class, 'add'])->name('add');
    Route::post('/update',             [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{id}',      [CartController::class, 'remove'])->name('remove');
    Route::post('/checkout',           [CartController::class, 'checkout'])->name('checkout')->middleware('customer.auth');
});

// Thanh toán (demo QR - VNPay/Momo)
Route::prefix('payment')->name('payment.')->middleware('customer.auth')->group(function () {
    Route::get('/{id}',          [PaymentController::class, 'show'])->name('show');
    Route::post('/{id}/confirm', [PaymentController::class, 'confirm'])->name('confirm');
});

// Wishlist
Route::prefix('wishlist')->name('wishlist.')->group(function () {
    Route::get('/',               [WishlistController::class, 'index'])->name('index');
    Route::post('/add/{id}',      [WishlistController::class, 'add'])->name('add');
    Route::delete('/remove/{id}', [WishlistController::class, 'remove'])->name('remove');
});

// ═══════════════════════════════════════════════════
//  ADMIN
// ═══════════════════════════════════════════════════

Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/login',   [AdminLoginController::class, 'showForm'])->name('login');
    Route::post('/login',  [AdminLoginController::class, 'login'])->name('login.post');
    Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');

    Route::middleware('admin.auth')->group(function () {

        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('products',  AdminProductController::class);
        Route::resource('categories', AdminCategoryController::class);
        Route::resource('news',      AdminNewsController::class);
        Route::resource('users',     AdminUserController::class);

        Route::get('orders',               [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{id}',          [AdminOrderController::class, 'detail'])->name('orders.detail');
        Route::post('orders/{id}/deliver', [AdminOrderController::class, 'deliver'])->name('orders.deliver');
    });
});
