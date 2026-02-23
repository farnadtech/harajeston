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
Route::get('/register', function () { return view('auth.register'); })->name('register');
Route::post('/login', function (\Illuminate\Http\Request $request) {
    $login = $request->input('login');
    
    // Check if login is email or phone
    $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
    
    $credentials = [
        $fieldType => $login,
        'password' => $request->input('password')
    ];
    
    if (auth()->attempt($credentials, $request->filled('remember'))) {
        $request->session()->regenerate();
        return redirect()->intended('/dashboard');
    }
    
    return back()->withErrors([
        'login' => 'اطلاعات ورود نادرست است.',
    ])->withInput($request->only('login'));
});
Route::post('/register', function (\Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
        'terms' => 'required|accepted',
    ], [
        'name.required' => 'نام و نام خانوادگی الزامی است.',
        'phone.required' => 'شماره تلفن الزامی است.',
        'email.required' => 'ایمیل الزامی است.',
        'email.email' => 'فرمت ایمیل صحیح نیست.',
        'email.unique' => 'این ایمیل قبلاً ثبت شده است.',
        'password.required' => 'رمز عبور الزامی است.',
        'password.min' => 'رمز عبور باید حداقل ۸ کاراکتر باشد.',
        'password.confirmed' => 'تکرار رمز عبور مطابقت ندارد.',
        'terms.required' => 'باید قوانین و مقررات را بپذیرید.',
        'terms.accepted' => 'باید قوانین و مقررات را بپذیرید.',
    ]);
    
    // All new users start as buyers with no seller status
    $user = \App\Models\User::create([
        'name' => $validated['name'],
        'phone' => $validated['phone'],
        'email' => $validated['email'],
        'password' => bcrypt($validated['password']),
        'role' => 'buyer',
        'seller_status' => 'none',
    ]);
    
    // Create wallet
    \App\Models\Wallet::create([
        'user_id' => $user->id,
        'balance' => 0,
        'frozen' => 0,
    ]);
    
    auth()->login($user);
    
    return redirect('/dashboard');
});

Route::post('/logout', function () { auth()->logout(); return redirect('/'); })->name('logout');

// Password Reset Routes
Route::get('/password/request', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');

// API Routes
Route::get('/api/listings/search', [\App\Http\Controllers\Api\ListingController::class, 'search']);
Route::get('/api/categories/structure', [\App\Http\Controllers\Api\CategoryController::class, 'getStructure']);
Route::get('/api/categories/{category}/attributes', [\App\Http\Controllers\Api\CategoryController::class, 'getAttributes']);
Route::get('/api/categories/{category}/path', [\App\Http\Controllers\Api\CategoryController::class, 'getPath']);

// Listings
Route::get('/listings', [ListingController::class, 'index'])->name('listings.index');

// Comments (Public - requires auth)
Route::middleware('auth')->group(function () {
    Route::post('/listings/{listing}/comments', [\App\Http\Controllers\ListingCommentController::class, 'store'])->name('listings.comments.store');
});

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Seller Request
    Route::get('/become-seller', [\App\Http\Controllers\SellerRequestController::class, 'create'])->name('seller-request.create');
    Route::post('/become-seller', [\App\Http\Controllers\SellerRequestController::class, 'store'])->name('seller-request.store');
    Route::get('/seller-request/status', [\App\Http\Controllers\SellerRequestController::class, 'status'])->name('seller-request.status');
    
    // Listings (Authenticated) - IMPORTANT: /listings/create must come BEFORE /listings/{listing}
    Route::get('/my-listings', [ListingController::class, 'myListings'])->name('my-listings');
    Route::get('/listings/create', [ListingController::class, 'create'])->name('listings.create');
    Route::get('/listings/{listing}/edit', [ListingController::class, 'edit'])->name('listings.edit');
    
    Route::post('/listings', [ListingController::class, 'store'])->name('listings.store');
    Route::put('/listings/{listing}', [ListingController::class, 'update'])->name('listings.update');
    Route::post('/listings/{listing}/participate', [ListingController::class, 'participate'])->name('listings.participate');
});

// Listings show route - MUST come AFTER /listings/create to avoid conflicts
Route::get('/listings/{listing}', [ListingController::class, 'show'])->name('listings.show');

Route::middleware('auth')->group(function () {
    // Bidding
    Route::post('/bids', [BidController::class, 'store'])
        ->name('bids.store')
        ->middleware('throttle:bids');
    
    // Payment
    Route::post('/payment/complete', [PaymentController::class, 'complete'])->name('payment.complete');
    
    // Wallet
    Route::get('/wallet', [WalletController::class, 'show'])->name('wallet.show');
    Route::post('/wallet/add-funds', [WalletController::class, 'addFunds'])->name('wallet.add-funds');
    Route::post('/wallet/withdraw', [WalletController::class, 'withdraw'])->name('wallet.withdraw');
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
    
    // User Notifications (non-admin)
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('user.notifications.index');
    Route::get('/notifications/recent', [\App\Http\Controllers\NotificationController::class, 'getRecent'])->name('user.notifications.recent');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('user.notifications.read');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('user.notifications.mark-all-read');
    
    // Admin Routes
    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        
        // Settings
        Route::get('/settings', [SettingsController::class, 'index'])->name('admin.settings.index');
        Route::put('/settings/deposit', [SettingsController::class, 'updateDeposit'])->name('admin.settings.deposit.update');
        Route::put('/settings/commission', [SettingsController::class, 'updateCommission'])->name('admin.settings.commission.update');
        Route::put('/settings/seller', [SettingsController::class, 'updateSeller'])->name('admin.settings.seller.update');
        Route::put('/settings/auction-duration', [SettingsController::class, 'updateAuctionDuration'])->name('admin.settings.auction-duration.update');
        Route::put('/settings/wallet', [SettingsController::class, 'updateWallet'])->name('admin.settings.wallet.update');
        Route::put('/settings/loser-fee', [SettingsController::class, 'updateLoserFee'])->name('admin.settings.loser-fee.update');
        Route::put('/settings/forfeit', [SettingsController::class, 'updateForfeit'])->name('admin.settings.forfeit.update');
        Route::put('/settings/listing', [SettingsController::class, 'updateListing'])->name('admin.settings.listing.update');

        // Category Commissions
        Route::get('/category-commissions', [\App\Http\Controllers\Admin\CategoryCommissionController::class, 'index'])->name('admin.category-commissions.index');
        Route::post('/category-commissions', [\App\Http\Controllers\Admin\CategoryCommissionController::class, 'store'])->name('admin.category-commissions.store');
        Route::delete('/category-commissions/{id}', [\App\Http\Controllers\Admin\CategoryCommissionController::class, 'destroy'])->name('admin.category-commissions.destroy');

        
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
        
        // Admin Listings Resource (using slug for SEO)
        Route::get('/listings', [AdminListingController::class, 'index'])->name('admin.listings.index');
        Route::get('/listings/create', [AdminListingController::class, 'create'])->name('admin.listings.create');
        Route::post('/listings', [AdminListingController::class, 'store'])->name('admin.listings.store');
        Route::get('/listings/{listing}', [AdminListingController::class, 'show'])->name('admin.listings.show');
        Route::get('/listings/{listing}/edit', [AdminListingController::class, 'edit'])->name('admin.listings.edit');
        Route::put('/listings/{listing}', [AdminListingController::class, 'update'])->name('admin.listings.update');
        Route::delete('/listings/{listing}', [AdminListingController::class, 'destroy'])->name('admin.listings.destroy');
        
        // Auction Management Routes
        Route::get('/listings/{listing}/manage', [AdminListingController::class, 'manage'])->name('admin.listings.manage');
        Route::put('/listings/{listing}/settings', [AdminListingController::class, 'updateSettings'])->name('admin.listings.settings');
        Route::post('/listings/{listing}/end-early', [AdminListingController::class, 'endEarly'])->name('admin.listings.end-early');
        Route::post('/listings/{listing}/suspend', [AdminListingController::class, 'suspend'])->name('admin.listings.suspend');
        Route::post('/listings/{listing}/activate', [AdminListingController::class, 'activate'])->name('admin.listings.activate');
        Route::post('/listings/{listing}/approve', [AdminListingController::class, 'approve'])->name('admin.listings.approve');
        Route::post('/listings/{listing}/reject', [AdminListingController::class, 'reject'])->name('admin.listings.reject');
        Route::put('/listings/{listing}/tags', [AdminListingController::class, 'updateTags'])->name('admin.listings.tags');
        Route::get('/listings/{listing}/bids', [AdminListingController::class, 'getBids'])->name('admin.listings.bids');
        Route::post('/listings/{listing}/images', [AdminListingController::class, 'uploadImage'])->name('admin.listings.images.upload');
        Route::delete('/listings/{listing}/images/{image}', [AdminListingController::class, 'deleteImage'])->name('admin.listings.images.delete');
        Route::post('/listings/{listing}/images/{image}/set-main', [AdminListingController::class, 'setMainImage'])->name('admin.listings.images.set-main');
        
        // Bid Management
        Route::post('/bids/{bid}/cancel', [\App\Http\Controllers\Admin\BidController::class, 'cancel'])->name('admin.bids.cancel');
        
        // User Management
        Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.users.index');
        Route::get('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('admin.users.show');
        Route::post('/users/{user}/suspend', [\App\Http\Controllers\Admin\UserController::class, 'suspend'])->name('admin.users.suspend');
        Route::post('/users/{user}/activate', [\App\Http\Controllers\Admin\UserController::class, 'activate'])->name('admin.users.activate');
        Route::post('/users/{user}/verify-email', [\App\Http\Controllers\Admin\UserController::class, 'verifyEmail'])->name('admin.users.verify-email');
        
        // Seller Management
        Route::get('/sellers', [\App\Http\Controllers\Admin\SellerController::class, 'index'])->name('admin.sellers.index');
        Route::get('/sellers/{seller}', [\App\Http\Controllers\Admin\SellerController::class, 'show'])->name('admin.sellers.show');
        Route::post('/sellers/{seller}/approve', [\App\Http\Controllers\Admin\SellerController::class, 'approve'])->name('admin.sellers.approve');
        Route::post('/sellers/{seller}/reject', [\App\Http\Controllers\Admin\SellerController::class, 'reject'])->name('admin.sellers.reject');
        Route::post('/sellers/{seller}/suspend', [\App\Http\Controllers\Admin\SellerController::class, 'suspend'])->name('admin.sellers.suspend');
        Route::post('/sellers/{seller}/activate', [\App\Http\Controllers\Admin\SellerController::class, 'activate'])->name('admin.sellers.activate');
        Route::delete('/sellers/{seller}', [\App\Http\Controllers\Admin\SellerController::class, 'destroy'])->name('admin.sellers.destroy');
        
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
        Route::post('/shipping-methods/{shippingMethod}/toggle', [ShippingMethodController::class, 'toggle'])->name('admin.shipping-methods.toggle');
        Route::resource('orders', AdminOrderController::class, ['as' => 'admin'])->only(['index', 'show']);
    });
});
