<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StlController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Pages
|--------------------------------------------------------------------------
*/
Route::get('/', [MainController::class, 'index'])->name('main.index');
Route::get('/products', [MainController::class, 'products'])->name('main.products');
Route::get('/plans', [MainController::class, 'plans'])->name('main.plans');
Route::get('/contact', [MainController::class, 'contact'])->name('main.contact');
Route::post('/contact', [MainController::class, 'submitContact'])->name('main.contact.submit');
Route::get('/stl/{id}', [StlController::class, 'show'])->name('stl.show');

/*
|--------------------------------------------------------------------------
| Cart & Checkout
|--------------------------------------------------------------------------
*/
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
Route::post('/checkout/submit', [OrderController::class, 'submit'])->name('checkout.submit');
Route::get('/thankyou', [OrderController::class, 'thankyou'])->name('thankyou');

/*
|--------------------------------------------------------------------------
| Admin Authentication
|--------------------------------------------------------------------------
*/
Route::get('/admin/login', [AdminController::class, 'login'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'checkLogin'])->name('admin.checkLogin');
Route::get('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');

/*
|--------------------------------------------------------------------------
| Admin Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth.admin')->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Products CRUD
    Route::get('/admin/products', [ProductController::class, 'index'])->name('admin.products.index');
    Route::get('/admin/products/create', [ProductController::class, 'create'])->name('admin.products.create');
    Route::post('/admin/products', [ProductController::class, 'store'])->name('admin.products.store');
    Route::get('/admin/products/{id}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
    Route::put('/admin/products/{id}', [ProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/admin/products/{id}', [ProductController::class, 'destroy'])->name('admin.products.destroy');

    // Plans CRUD
    Route::get('/admin/plans', [PlanController::class, 'index'])->name('admin.plans.index');
    Route::get('/admin/plans/create', [PlanController::class, 'create'])->name('admin.plans.create');
    Route::post('/admin/plans', [PlanController::class, 'store'])->name('admin.plans.store');
    Route::get('/admin/plans/{id}/edit', [PlanController::class, 'edit'])->name('admin.plans.edit');
    Route::put('/admin/plans/{id}', [PlanController::class, 'update'])->name('admin.plans.update');
    Route::delete('/admin/plans/{id}', [PlanController::class, 'destroy'])->name('admin.plans.destroy');

    // Orders management
    Route::get('/admin/orders', [OrderController::class, 'adminIndex'])->name('admin.orders.index');
    Route::get('/admin/orders/{orderKey}', [OrderController::class, 'adminShow'])->name('admin.orders.show');
    Route::get('/admin/orders/{orderKey}/edit', [OrderController::class, 'adminEdit'])->name('admin.orders.edit');
    Route::put('/admin/orders/{orderKey}', [OrderController::class, 'adminUpdate'])->name('admin.orders.update');
    Route::delete('/admin/orders/{orderKey}', [OrderController::class, 'adminDestroy'])->name('admin.orders.destroy');

    // Settings
    Route::get('/admin/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::put('/admin/settings', [AdminController::class, 'updateSettings'])->name('admin.settings.update');

    // Messages
    Route::get('/admin/messages', [AdminController::class, 'messages'])->name('admin.messages.index');
    Route::delete('/admin/messages/{id}', [AdminController::class, 'deleteMessage'])->name('admin.messages.delete');
});
