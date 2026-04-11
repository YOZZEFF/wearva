<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductImageController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

// start  Categories Section

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('categories/{category}', [CategoryController::class, 'show']);

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {

    Route::post('admin/categories', [CategoryController::class, 'store']);
    Route::put('admin/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('admin/categories/{category}', [CategoryController::class, 'destroy']);
});
//  end Categories section

// start Products Section
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {

     Route::post('admin/products', [ProductController::class,'store']);
     Route::put('admin/products/{product}', [ProductController::class,'update']);
     Route::delete('admin/products/{product}', [ProductController::class,'destroy']);

    //  Product Images

    Route::post('admin/product/{product}/images',[ProductImageController::class,'store']);
    Route::put('admin/product/{product}/images/{image}/primary',[ProductImageController::class,'setPrimary']);
    Route::delete('admin/product/{product}/images/{image}',[ProductImageController::class,'destroy']);


});
// end Products Section









