<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\InventoryController;
use Illuminate\Support\Facades\Auth;

Route::get('/', [ItemController::class, 'getItems'])->name('getItems');
// legacy name used in views copied from pawmart
Route::get('/products', [ItemController::class, 'getItems'])->name('products.index');
Route::get('/add-to-cart/{id}', [ItemController::class, 'addToCart'])->name('addToCart');
Route::post('/add-to-cart/{id}', [ItemController::class, 'addToCart'])->name('cart.add');
Route::get('/shopping-cart', [ItemController::class, 'getCart'])->name('getCart');
Route::get('/reduce/{id}', [ItemController::class, 'getReduceByOne'])->name('reduceByOne');
Route::post('/cart/update/{id}', [ItemController::class, 'updateCartQuantity'])->name('updateCartQuantity');
Route::get('/remove/{id}', [ItemController::class, 'getRemoveItem'])->name('removeItem');
Route::post('/checkout', [ItemController::class, 'postCheckout'])->name('checkout');
Route::get('/order-history', [ItemController::class, 'orderHistory'])->name('orderHistory');
Route::get('/order/{id}', [ItemController::class, 'orderDetails'])->name('orderDetails');

// product details and reviews
Route::get('/product/{id}', [ItemController::class, 'show'])->name('product.show');
Route::post('/product/{id}/review', [\App\Http\Controllers\ReviewController::class, 'store'])->name('product.review.store');

// user profile (namespaced to avoid collisions)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\UserProfileController::class, 'edit'])
        ->name('user.profile.edit');
    Route::put('/profile', [\App\Http\Controllers\UserProfileController::class, 'update'])
        ->name('user.profile.update');
    Route::delete('/profile/images/{image}', [\App\Http\Controllers\UserProfileController::class, 'deleteImage'])
        ->name('user.profile.images.destroy');
    // saved addresses
    Route::get('/addresses', [\App\Http\Controllers\UserAddressController::class, 'index'])->name('addresses.index');
    Route::post('/addresses', [\App\Http\Controllers\UserAddressController::class, 'store'])->name('addresses.store');
    Route::delete('/addresses/{address}', [\App\Http\Controllers\UserAddressController::class, 'destroy'])->name('addresses.destroy');
});

// Only call Auth::routes() if the laravel/ui package is installed.
// This avoids runtime errors when artisan commands run but the UI package
// hasn't been added to the project (common in copied projects).
if (class_exists(\Laravel\Ui\UiServiceProvider::class)) {
    Auth::routes();
}

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard.index');
    
    // Products
    Route::get('/products', [ProductController::class, 'index'])->name('admin.products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('admin.products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('admin.products.store');
    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('admin.products.destroy');
    Route::delete('/products/{product}/images/{image}', [ProductController::class, 'destroyImage'])->name('admin.products.images.destroy');
    
    // Categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('admin.categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])->name('admin.categories.edit');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');
    
    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('admin.orders.show');
    Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');
    
    // Users
    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('admin.users.show');
    Route::put('/users/{id}/status', [UserController::class, 'updateStatus'])->name('admin.users.updateStatus');
    // additional management routes (edit/delete/restore) used by views
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    Route::post('/users/{id}/restore', [UserController::class, 'restore'])->name('admin.dashboard.users.restore');
    
    // Reports
    Route::get('/reports/sales', [ReportController::class, 'sales'])->name('admin.reports.sales');
    Route::get('/reports/inventory', [ReportController::class, 'inventory'])->name('admin.reports.inventory');
    Route::get('/reports/customer-metrics', [ReportController::class, 'customerMetrics'])->name('admin.reports.customer-metrics');
    
    // Inventory
    Route::get('/inventory/low-stock', [InventoryController::class, 'lowStock'])->name('admin.inventory.low-stock');
    Route::get('/inventory/summary', [InventoryController::class, 'summary'])->name('admin.inventory.summary');

    // review moderation
    Route::get('/reviews', [\App\Http\Controllers\Admin\ReviewModerationController::class, 'index'])->name('admin.reviews.index');
    Route::get('/reviews/{review}', [\App\Http\Controllers\Admin\ReviewModerationController::class, 'show'])->name('admin.reviews.show');
    Route::put('/reviews/{review}/approve', [\App\Http\Controllers\Admin\ReviewModerationController::class, 'approve'])->name('admin.reviews.approve');
    Route::put('/reviews/{review}/reject', [\App\Http\Controllers\Admin\ReviewModerationController::class, 'reject'])->name('admin.reviews.reject');
    Route::put('/reviews/{review}/toggle', [\App\Http\Controllers\Admin\ReviewModerationController::class, 'toggle'])->name('admin.reviews.toggle');
});

