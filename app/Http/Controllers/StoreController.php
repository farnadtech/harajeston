<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateStoreRequest;
use App\Http\Requests\UploadStoreBannerRequest;
use App\Http\Requests\UploadStoreLogoRequest;
use App\Models\Store;
use App\Services\StoreService;

class StoreController extends Controller
{
    public function __construct(
        protected StoreService $storeService
    ) {}

    /**
     * Display public storefront
     */
    public function show(string $username)
    {
        $store = $this->storeService->getStoreBySlug($username);

        if (!$store) {
            abort(404, 'فروشگاه یافت نشد.');
        }

        // Load seller with rating
        $seller = $store->user;

        // Get sort and tab parameters
        $sort = request('sort', 'newest');
        $tab = request('tab', 'active');

        // همه محصولات حراج هستند
        $query = $store->listings()->with('images');

        // Filter by tab
        switch ($tab) {
            case 'ended':
                // حراج‌های تمام شده
                $query->where('status', 'completed');
                break;
            case 'upcoming':
                // حراج‌های آینده
                $query->where('status', 'pending');
                break;
            case 'active':
            default:
                // حراج‌های فعال
                $query->where('status', 'active');
                break;
        }

        // Apply sorting
        switch ($sort) {
            case 'price_asc':
                $query->orderByRaw('COALESCE(current_price, starting_price) ASC');
                break;
            case 'price_desc':
                $query->orderByRaw('COALESCE(current_price, starting_price) DESC');
                break;
            case 'ending_soon':
                $query->orderBy('ends_at', 'asc');
                break;
            case 'starting_soon':
                $query->orderBy('starts_at', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $listings = $query->paginate(20)->appends(['sort' => $sort, 'tab' => $tab]);

        // Load approved reviews
        $reviews = $seller->sellerReviews()
            ->approved()
            ->with(['buyer', 'order'])
            ->latest()
            ->paginate(10, ['*'], 'reviews_page');

        // Calculate rating distribution (all reviews, not just paginated)
        $ratingCounts = $seller->sellerReviews()
            ->approved()
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating');

        // Calculate stats - فروش‌های موفق شامل سفارشات تحویل داده شده و تکمیل شده
        $completedSales = \App\Models\Order::where('seller_id', $seller->id)
            ->whereIn('status', ['delivered', 'completed'])
            ->count();

        return view('stores.show', compact('store', 'listings', 'seller', 'reviews', 'completedSales', 'ratingCounts', 'sort', 'tab'));
    }

    /**
     * Show storefront customization form
     */
    public function edit()
    {
        $store = auth()->user()->store;

        if (!$store) {
            abort(404, 'فروشگاه یافت نشد.');
        }

        return view('stores.edit', compact('store'));
    }

    /**
     * Update storefront profile
     */
    public function update(UpdateStoreRequest $request)
    {
        $store = auth()->user()->store;

        $this->storeService->updateStoreProfile($store, $request->validated());

        return redirect()
            ->route('stores.edit')
            ->with('success', 'فروشگاه با موفقیت به‌روزرسانی شد.');
    }

    /**
     * Upload store banner
     */
    public function uploadBanner(UploadStoreBannerRequest $request)
    {
        $store = auth()->user()->store;

        $this->storeService->updateStoreProfile($store, [
            'banner' => $request->file('banner'),
        ]);

        return redirect()
            ->route('stores.edit')
            ->with('success', 'بنر با موفقیت آپلود شد.');
    }

    /**
     * Upload store logo
     */
    public function uploadLogo(UploadStoreLogoRequest $request)
    {
        $store = auth()->user()->store;

        $this->storeService->updateStoreProfile($store, [
            'logo' => $request->file('logo'),
        ]);

        return redirect()
            ->route('stores.edit')
            ->with('success', 'لوگو با موفقیت آپلود شد.');
    }
}
