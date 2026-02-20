<?php

namespace App\Services;

use App\Models\ShippingMethod;
use App\Models\Listing;
use App\Models\User;
use App\Exceptions\Shipping\ShippingMethodNotFoundException;

class ShippingService
{
    /**
     * Create shipping method (admin only)
     */
    public function createShippingMethod(User $admin, array $data): ShippingMethod
    {
        return ShippingMethod::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'base_cost' => $data['base_cost'],
            'is_active' => $data['is_active'] ?? true,
            'created_by' => $admin->id,
        ]);
    }

    /**
     * Attach shipping methods to listing
     */
    public function attachShippingToListing(Listing $listing, array $shippingMethodIds, array $customCosts = []): void
    {
        $syncData = [];
        
        foreach ($shippingMethodIds as $methodId) {
            $syncData[$methodId] = [
                'custom_cost_adjustment' => $customCosts[$methodId] ?? null,
            ];
        }
        
        $listing->shippingMethods()->sync($syncData);
    }

    /**
     * Calculate shipping cost for listing
     */
    public function calculateShippingCost(Listing $listing, int $shippingMethodId): float
    {
        $shippingMethod = ShippingMethod::find($shippingMethodId);
        
        if (!$shippingMethod) {
            throw new ShippingMethodNotFoundException('روش ارسال یافت نشد.');
        }
        
        $baseCost = $shippingMethod->base_cost;
        
        // Check for custom adjustment
        $pivot = $listing->shippingMethods()
            ->where('shipping_method_id', $shippingMethodId)
            ->first();
        
        if ($pivot && $pivot->pivot->custom_cost_adjustment) {
            return $baseCost + $pivot->pivot->custom_cost_adjustment;
        }
        
        return $baseCost;
    }

    /**
     * Get active shipping methods
     */
    public function getActiveShippingMethods()
    {
        return ShippingMethod::where('is_active', true)->get();
    }

    /**
     * Get all shipping methods (admin)
     */
    public function getAllShippingMethods()
    {
        return ShippingMethod::all();
    }
}
