<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminCustomerController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminForgotPasswordController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\AdminNewsController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminVoucherController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/{id}', [ProductController::class, 'detail'])->name('detail');
    Route::post('/{id}/rating', [ProductController::class, 'rating'])->name('rating');
});

Route::get('/search', [SearchController::class, 'index'])->name('search');

Route::prefix('news')->name('news.')->group(function () {
    Route::get('/', [NewsController::class, 'index'])->name('index');
    Route::get('/{id}', [NewsController::class, 'detail'])->name('detail');
});

Route::get('/contact', [ContactController::class, 'index'])->name('contact');

Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/policy', [PageController::class, 'policy'])->name('policy');

Route::prefix('orders')->name('orders.')->middleware('customer.auth')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('index');
    Route::get('/{id}', [OrderController::class, 'detail'])->name('detail');
});

Route::prefix('account')->name('account.')->group(function () {
    Route::get('/login', [AccountController::class, 'loginForm'])->name('login');
    Route::post('/login', [AccountController::class, 'login'])->name('login.post')->middleware('throttle:5,1');
    Route::get('/register', [AccountController::class, 'registerForm'])->name('register');
    Route::post('/register', [AccountController::class, 'register'])->name('register.post')->middleware('throttle:5,1');
    Route::post('/logout', [AccountController::class, 'logout'])->name('logout');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'send'])->name('password.email')->middleware('throttle:5,1');
    Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update')->middleware('throttle:5,1');

    Route::middleware('customer.auth')->group(function () {
        Route::get('/profile', [AccountController::class, 'profile'])->name('profile');
        Route::post('/profile', [AccountController::class, 'updateProfile'])->name('profile.update');
        Route::post('/change-password', [AccountController::class, 'changePassword'])->name('password.change');
    });
});

Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add/{id}', [CartController::class, 'add'])->name('add');
    Route::post('/update', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{id}', [CartController::class, 'remove'])->name('remove');
    Route::post('/preview', [CartController::class, 'preview'])->name('preview');
    Route::post('/checkout', [CartController::class, 'checkout'])->name('checkout')->middleware('customer.auth');
});

Route::prefix('payment')->name('payment.')->middleware('customer.auth')->group(function () {
    Route::get('/{id}', [PaymentController::class, 'show'])->name('show');
    Route::post('/{id}/confirm', [PaymentController::class, 'confirm'])->name('confirm');
});

Route::prefix('wishlist')->name('wishlist.')->group(function () {
    Route::get('/', [WishlistController::class, 'index'])->name('index');
    Route::post('/add/{id}', [WishlistController::class, 'add'])->name('add');
    Route::delete('/remove/{id}', [WishlistController::class, 'remove'])->name('remove');
});

Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/login', [AdminLoginController::class, 'showForm'])->name('login');
    Route::post('/login', [AdminLoginController::class, 'login'])->name('login.post')->middleware('throttle:5,1');
    Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');

    Route::get('/forgot-password', [AdminForgotPasswordController::class, 'showForm'])->name('password.request');
    Route::post('/forgot-password', [AdminForgotPasswordController::class, 'send'])->name('password.email')->middleware('throttle:5,1');
    Route::get('/reset-password/{token}', [AdminForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AdminForgotPasswordController::class, 'reset'])->name('password.update')->middleware('throttle:5,1');

    Route::middleware('admin.auth')->group(function () {

        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('products', AdminProductController::class);
        Route::resource('categories', AdminCategoryController::class);
        Route::resource('news', AdminNewsController::class);
        Route::resource('users', AdminUserController::class);

        Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{id}', [AdminOrderController::class, 'detail'])->name('orders.detail');
        Route::post('orders/{id}/deliver', [AdminOrderController::class, 'deliver'])->name('orders.deliver');
        Route::post('orders/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
        Route::get('orders/export/csv', [AdminOrderController::class, 'exportCsv'])->name('orders.export');

        Route::resource('vouchers', AdminVoucherController::class);

        Route::prefix('customers')->name('customers.')->group(function () {
            Route::get('/', [AdminCustomerController::class, 'index'])->name('index');
            Route::get('/{id}', [AdminCustomerController::class, 'show'])->name('show');
            Route::post('/{id}/toggle-active', [AdminCustomerController::class, 'toggleActive'])->name('toggle-active');
        });
    });
});
