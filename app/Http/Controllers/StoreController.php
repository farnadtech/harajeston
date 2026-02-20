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

        // همه محصولات حراج هستند
        $listings = $store->listings()
            ->where('status', 'active')
            ->with('images')
            ->orderBy('ends_at', 'asc') // نزدیک‌ترین به پایان اول
            ->paginate(20);

        return view('stores.show', compact('store', 'listings'));
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
