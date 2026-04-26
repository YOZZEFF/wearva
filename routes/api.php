<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductImageController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\OrderController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Start  customer  Cart

    Route::get('/cart',[CartController::class, 'index']);
    Route::post('/cart',[CartController::class, 'store']);
    Route::put('/cart/{cartItem}',[CartController::class, 'update']);
    Route::delete('/cart/{cartItem}',[CartController::class, 'destroy']);

    // End customer Cart
    // start customer wishlist

    Route::get('/wishlist',[WishlistController::class, 'index']);
    Route::post('/wishlist',[WishlistController::class, 'store']);
    Route::delete('/wishlist/{product}',[WishlistController::class, 'destroy']);

   // end customer wishlist

   //start customer address

    Route::get('/address',[AddressController::class, 'index']);
    Route::post('/address',[AddressController::class, 'store']);
    Route::put('/address/{address}',[AddressController::class, 'update']);
    Route::delete('/address/{address}',[AddressController::class, 'destroy']);

    // end customer address

        // start customer orders

        Route::get('/orders',[OrderController::class, 'index']);
        Route::post('/orders',[OrderController::class, 'store']);
        Route::get('/orders/{order}', [OrderController::class, 'show']);

        // end customer orders

});

// start  customer  Categories Section
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('categories/{category}', [CategoryController::class, 'show']);
// end  customer  Categories Section

// start customer Products Section
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);
// end customer Products Section

// admin zone
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {

    //  Categories Section
    Route::post('admin/categories', [CategoryController::class, 'store']);
    Route::put('admin/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('admin/categories/{category}', [CategoryController::class, 'destroy']);
    // End Categories Section

    //  Products Section
     Route::post('admin/products', [ProductController::class,'store']);
     Route::put('admin/products/{product}', [ProductController::class,'update']);
     Route::delete('admin/products/{product}', [ProductController::class,'destroy']);
    //  End Products Section

    //  Product Images Section

    Route::post('admin/product/{product}/images',[ProductImageController::class,'store']);
    Route::put('admin/product/{product}/images/{image}/primary',[ProductImageController::class,'setPrimary']);
    Route::delete('admin/product/{product}/images/{image}',[ProductImageController::class,'destroy']);

    //  End Product Images Section

    //  Orders Section
    Route::get('/admin/orders', [OrderController::class, 'Adminindex']);
    Route::get('/admin/orders/{order}', [OrderController::class, 'show']);
    Route::put('/admin/orders/{order}/status', [OrderController::class, 'AdminUpdateStatus']);
    //  End Orders Section



});
// end admin zone











