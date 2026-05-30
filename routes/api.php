<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\ProductImageController;
use App\Http\Controllers\Api\ProductVariantController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\AdminOrderController;

//
Route::prefix('v1')
    ->middleware(['throttle:api', 'api.logger'])
    ->group(function () {

        Route::middleware('throttle:auth')->group(function () {
            Route::post('/register', [AuthController::class, 'register']);
            Route::post('/login', [AuthController::class, 'login']);
        });

        Route::apiResource('/categories', CategoryController::class);
        Route::apiResource('/brands', BrandController::class);
        Route::apiResource('/products', ProductController::class);
        Route::apiResource('/products/{product}/images', ProductImageController::class);
        Route::apiResource('/products/{product}/variants', ProductVariantController::class);

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/profile', [AuthController::class, 'profile']);

            Route::get('/cart', [CartController::class, 'index']);
            Route::post('/cart', [CartController::class, 'store']);
            Route::put('/cart/{cart}', [CartController::class, 'update']);
            Route::delete('/cart/{cart}', [CartController::class, 'destroy']);
            Route::delete('/cart', [CartController::class, 'clear']);

            Route::get('/wishlist', [WishlistController::class, 'index']);
            Route::post('/wishlist', [WishlistController::class, 'store']);
            Route::delete('/wishlist/{wishlist}', [WishlistController::class, 'destroy']);
            Route::delete('/wishlist/product/{product}', [WishlistController::class, 'destroyByProduct']);

            Route::apiResource('/addresses', AddressController::class);
            Route::patch('/addresses/{address}/default', [AddressController::class, 'setDefault']);

            Route::post('/checkout', [CheckoutController::class, 'store']);
            Route::get('/orders', [OrderController::class, 'index']);
            Route::get('/orders/{order}', [OrderController::class, 'show']);
        });

        Route::middleware(['auth:sanctum', 'admin', 'throttle:admin'])
            ->prefix('admin')
            ->group(function () {
                Route::get('/orders', [AdminOrderController::class, 'index']);
                Route::get('/orders/{order}', [AdminOrderController::class, 'show']);
                Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus']);
                Route::patch('/orders/{order}/payment-status', [AdminOrderController::class, 'updatePaymentStatus']);
            });
    });
