<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ListingController, BidController, PaymentController, WalletController,
    StoreController, CartController, CheckoutController, OrderController, DashboardController
};
use App\Http\Controllers\Admin\{
    ListingController as AdminListingController, 
    ShippingMethodController, 
    OrderController as AdminOrderController,
    AdminController,
    SettingsController,
    FinancialReportController,
    CategoryController
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [ListingController::class, 'index'])->name('home');

// Authentication Routes
Route::get('/login', function () { return view('auth.login'); })->name('login');
Route::post('/login', function (\Illuminate\Http\Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);
    
    if (auth()->attempt($credentials, $request->filled('remember'))) {
        $request->session()->regenerate();
        return redirect()->intended('/dashboard');
    }
    
    return back()->withErrors([
        'email' => 'اطلاعات ورود نادرست است.',
    ])->onlyInput('email');
});

Route::get('/register', function () { return view('auth.register'); })->name('register');
Route::post('/register', function (\Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
        'role' => 'required|in:buyer,seller',
        'username' => 'nullable|string|max:255|unique:users',
    ]);
    
    $user = \App\Models\User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => bcrypt($validated['password']),
        'role' => $validated['role'],
        'username' => $validated['username'] ?? null,
    ]);
    
    // Create wallet
    \App\Models\Wallet::create([
        'user_id' => $user->id,
        'balance' => 0,
        'frozen' => 0,
    ]);
    
    // Create store for sellers
    if ($user->role === 'seller') {
        $storeService = app(\App\Services\StoreService::class);
        $storeService->createStore($user);
    }
    
    auth()->login($user);
    
    return redirect('/dashboard');
});

Route::post('/logout', function () { auth()->logout(); return redirect('/'); })->name('logout');
Route::get('/password/request', function () { return view('auth.forgot-password'); })->name('password.request');

// API Routes
Route::get('/api/categories/structure', [\App\Http\Controllers\Api\CategoryController::class, 'getStructure']);
Route::get('/api/categories/{category}/attributes', [\App\Http\Controllers\Api\CategoryController::class, 'getAttributes']);
Route::get('/api/categories/{category}/path', [\App\Http\Controllers\Api\CategoryController::class, 'getPath']);

// Listings
Route::get('/listings', [ListingController::class, 'index'])->name('listings.index');
Route::get('/listings/{listing}', [ListingController::class, 'show'])->name('listings.show');

// Comments (Public - requires auth)
Route::middleware('auth')->group(function () {
    Route::post('/listings/{listing}/comments', [\App\Http\Controllers\ListingCommentController::class, 'store'])->name('listings.comments.store');
});

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Listings (Authenticated)
    Route::get('/listings/create', [ListingController::class, 'create'])->name('listings.create');
    Route::post('/listings', [ListingController::class, 'store'])->name('listings.store');
    Route::post('/listings/{listing}/participate', [ListingController::class, 'participate'])->name('listings.participate');
    
    // Bidding
    Route::post('/bids', [BidController::class, 'store'])
        ->name('bids.store')
        ->middleware('throttle:bids');
    
    // Payment
    Route::post('/payment/complete', [PaymentController::class, 'complete'])->name('payment.complete');
    
    // Wallet
    Route::get('/wallet', [WalletController::class, 'show'])->name('wallet.show');
    Route::post('/wallet/add-funds', [WalletController::class, 'addFunds'])->name('wallet.add-funds');
    Route::get('/wallet/export', [WalletController::class, 'export'])->name('wallet.export');
    
    // Store
    Route::get('/store/{username}', [StoreController::class, 'show'])->name('stores.show');
    Route::get('/stores/edit', [StoreController::class, 'edit'])->name('stores.edit');
    Route::put('/stores', [StoreController::class, 'update'])->name('stores.update');
    Route::post('/stores/upload-banner', [StoreController::class, 'uploadBanner'])->name('stores.upload-banner');
    Route::post('/stores/upload-logo', [StoreController::class, 'uploadLogo'])->name('stores.upload-logo');
    
    // Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::put('/cart/{item}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{item}', [CartController::class, 'remove'])->name('cart.remove');
    
    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    
    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    
    // Seller Reviews
    Route::get('/orders/{order}/review', [\App\Http\Controllers\SellerReviewController::class, 'create'])->name('seller-reviews.create');
    Route::post('/orders/{order}/review', [\App\Http\Controllers\SellerReviewController::class, 'store'])->name('seller-reviews.store');
    
    // Admin Routes
    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        
        // Settings
        Route::get('/settings', [SettingsController::class, 'index'])->name('admin.settings.index');
        Route::put('/settings/deposit', [SettingsController::class, 'updateDeposit'])->name('admin.settings.deposit.update');
        Route::put('/settings/commission', [SettingsController::class, 'updateCommission'])->name('admin.settings.commission.update');
        
        // Financial Reports
        Route::get('/financial-reports', [FinancialReportController::class, 'index'])->name('admin.financial-reports.index');
        Route::get('/financial-reports/commissions', [FinancialReportController::class, 'commissions'])->name('admin.financial-reports.commissions');
        Route::get('/financial-reports/export', [FinancialReportController::class, 'export'])->name('admin.financial-reports.export');
        Route::get('/financial-reports/chart-data', [FinancialReportController::class, 'chartData'])->name('admin.financial-reports.chart-data');
        
        // Categories
        Route::resource('categories', CategoryController::class, ['as' => 'admin']);
        Route::post('/categories/reorder', [CategoryController::class, 'reorder'])->name('admin.categories.reorder');
        
        // Category Attributes
        Route::get('/categories/{category}/attributes', [\App\Http\Controllers\Admin\CategoryAttributeController::class, 'index'])->name('admin.category-attributes.index');
        Route::get('/categories/{category}/attributes/create', [\App\Http\Controllers\Admin\CategoryAttributeController::class, 'create'])->name('admin.category-attributes.create');
        Route::post('/categories/{category}/attributes', [\App\Http\Controllers\Admin\CategoryAttributeController::class, 'store'])->name('admin.category-attributes.store');
        Route::get('/categories/{category}/attributes/{attribute}/edit', [\App\Http\Controllers\Admin\CategoryAttributeController::class, 'edit'])->name('admin.category-attributes.edit');
        Route::put('/categories/{category}/attributes/{attribute}', [\App\Http\Controllers\Admin\CategoryAttributeController::class, 'update'])->name('admin.category-attributes.update');
        Route::delete('/categories/{category}/attributes/{attribute}', [\App\Http\Controllers\Admin\CategoryAttributeController::class, 'destroy'])->name('admin.category-attributes.destroy');
        
        Route::resource('listings', AdminListingController::class, ['as' => 'admin']);
        
        // Auction Management Routes
        Route::get('/listings/{listing}/manage', [AdminListingController::class, 'manage'])->name('admin.listings.manage');
        Route::put('/listings/{listing}/settings', [AdminListingController::class, 'updateSettings'])->name('admin.listings.settings');
        Route::post('/listings/{listing}/end-early', [AdminListingController::class, 'endEarly'])->name('admin.listings.end-early');
        Route::post('/listings/{listing}/suspend', [AdminListingController::class, 'suspend'])->name('admin.listings.suspend');
        Route::post('/listings/{listing}/activate', [AdminListingController::class, 'activate'])->name('admin.listings.activate');
        Route::put('/listings/{listing}/tags', [AdminListingController::class, 'updateTags'])->name('admin.listings.tags');
        Route::get('/listings/{listing}/bids', [AdminListingController::class, 'getBids'])->name('admin.listings.bids');
        Route::post('/listings/{listing}/images', [AdminListingController::class, 'uploadImage'])->name('admin.listings.images.upload');
        Route::delete('/listings/{listing}/images/{image}', [AdminListingController::class, 'deleteImage'])->name('admin.listings.images.delete');
        
        // Bid Management
        Route::post('/bids/{bid}/cancel', [\App\Http\Controllers\Admin\BidController::class, 'cancel'])->name('admin.bids.cancel');
        
        // User Management
        Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.users.index');
        Route::get('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('admin.users.show');
        Route::post('/users/{user}/suspend', [\App\Http\Controllers\Admin\UserController::class, 'suspend'])->name('admin.users.suspend');
        Route::post('/users/{user}/activate', [\App\Http\Controllers\Admin\UserController::class, 'activate'])->name('admin.users.activate');
        Route::post('/users/{user}/verify-email', [\App\Http\Controllers\Admin\UserController::class, 'verifyEmail'])->name('admin.users.verify-email');
        
        // Notifications
        Route::get('/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('admin.notifications.index');
        Route::get('/notifications/recent', [\App\Http\Controllers\Admin\NotificationController::class, 'getRecent'])->name('admin.notifications.recent');
        Route::post('/notifications/{id}/read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('admin.notifications.read');
        Route::post('/notifications/mark-all-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('admin.notifications.mark-all-read');
        
        // Comments & Questions Management
        Route::get('/comments', [\App\Http\Controllers\Admin\CommentController::class, 'index'])->name('admin.comments.index');
        Route::post('/comments/{id}/approve', [\App\Http\Controllers\Admin\CommentController::class, 'approve'])->name('admin.comments.approve');
        Route::post('/comments/{id}/reject', [\App\Http\Controllers\Admin\CommentController::class, 'reject'])->name('admin.comments.reject');
        Route::delete('/comments/{id}', [\App\Http\Controllers\Admin\CommentController::class, 'destroy'])->name('admin.comments.destroy');
        
        // Seller Reviews Management
        Route::get('/seller-reviews', [\App\Http\Controllers\Admin\SellerReviewController::class, 'index'])->name('admin.seller-reviews.index');
        Route::post('/seller-reviews/{id}/approve', [\App\Http\Controllers\Admin\SellerReviewController::class, 'approve'])->name('admin.seller-reviews.approve');
        Route::post('/seller-reviews/{id}/reject', [\App\Http\Controllers\Admin\SellerReviewController::class, 'reject'])->name('admin.seller-reviews.reject');
        Route::delete('/seller-reviews/{id}', [\App\Http\Controllers\Admin\SellerReviewController::class, 'destroy'])->name('admin.seller-reviews.destroy');
        
        Route::resource('shipping-methods', ShippingMethodController::class, ['as' => 'admin']);
        Route::resource('orders', AdminOrderController::class, ['as' => 'admin'])->only(['index', 'show']);
    });
});
