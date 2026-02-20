<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateStoreRequest;
use App\Models\Store;
use App\Services\StoreService;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function __construct(
        private StoreService $storeService
    ) {}

    public function show($slug)
    {
        $store = $this->storeService->getStoreBySlug($slug);
        
        if (!$store) {
            return response()->json([
                'message' => 'فروشگاه یافت نشد',
            ], 404);
        }
        
        return response()->json([
            'store' => $store,
        ]);
    }

    public function update(UpdateStoreRequest $request, Store $store)
    {
        $this->authorize('update', $store);

        try {
            $updatedStore = $this->storeService->updateStoreProfile(
                $store->user,
                $request->only(['store_name', 'description'])
            );
            
            return response()->json([
                'message' => 'فروشگاه با موفقیت به‌روزرسانی شد',
                'store' => $updatedStore,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
