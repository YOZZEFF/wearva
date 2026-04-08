<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;

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



Route::middleware('auth:sanctum')->group(function () {


Route::post('categories', [CategoryController::class, 'store'])
        ->middleware('permission:store categories');

Route::put('categories/{category}', [CategoryController::class, 'update'])
        ->middleware('permission:update categories');

Route::delete('categories/{category}', [CategoryController::class, 'destroy'])
        ->middleware('permission:destroy categories');

 });
//  end Categories section

// start Products Section
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);







