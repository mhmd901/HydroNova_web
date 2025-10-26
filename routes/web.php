<?php

use Illuminate\Support\Facades\Route;

// Admin Controllers
use App\Http\Controllers\Admin\PlanController as AdminPlanController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;

// Frontend Controllers
use App\Http\Controllers\PlansController;
use App\Http\Controllers\ProductsController;

// -----------------------------
// FRONTEND ROUTES
// -----------------------------
Route::get('/', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

// Plans
Route::get('/plans', [PlansController::class, 'index'])->name('plans.index');
Route::get('/plans/{id}', [PlansController::class, 'show'])->name('plans.show');

// Products
Route::get('/products', [ProductsController::class, 'index'])->name('products.index');
Route::get('/products/{id}', [ProductsController::class, 'show'])->name('products.show');

// -----------------------------
// ADMIN ROUTES
// -----------------------------
Route::prefix('admin')->group(function () {
    Route::resource('plans', AdminPlanController::class);
    Route::resource('products', AdminProductController::class);
});
