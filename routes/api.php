<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BidController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\ListingController;
use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\Admin\ShippingMethodController as AdminShippingMethodController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public category routes
Route::get('/categories/structure', [CategoryController::class, 'getStructure']);
Route::get('/categories/{category}/attributes', [CategoryController::class, 'getAttributes']);
Route::get('/categories/{category}/path', [CategoryController::class, 'getPath']);

// Public store routes
Route::get('/stores/{slug}', [StoreController::class, 'show']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Listings
    Route::get('/listings', [ListingController::class, 'index']);
    Route::get('/listings/{listing}', [ListingController::class, 'show']);
    Route::post('/listings', [ListingController::class, 'store']);
    Route::post('/listings/{listing}/participate', [ListingController::class, 'participate']);
    
    // Images
    Route::post('/listings/images', [ImageController::class, 'upload']);
    
    // Bids
    Route::post('/listings/{listing}/bids', [BidController::class, 'store'])->middleware('throttle:bids');
    
    // Wallet
    Route::get('/wallet', [WalletController::class, 'show']);
    Route::get('/wallet/transactions', [WalletController::class, 'transactions']);
    Route::post('/wallet/add-funds', [WalletController::class, 'addFunds']);
    
    // Cart
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart/add', [CartController::class, 'add']);
    Route::put('/cart/items/{itemId}', [CartController::class, 'update']);
    Route::delete('/cart/items/{itemId}', [CartController::class, 'remove']);
    
    // Checkout
    Route::post('/checkout', [CheckoutController::class, 'process']);
    
    // Stores
    Route::put('/stores/{store}', [StoreController::class, 'update']);
    
    // Admin routes
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/shipping-methods', [AdminShippingMethodController::class, 'index']);
        Route::post('/shipping-methods', [AdminShippingMethodController::class, 'store']);
    });
});
