<?php

namespace App\Services;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class CacheService
{
    /**
     * Cache TTL in seconds (5 minutes)
     */
    const TTL = 300;

    /**
     * Get active listings with caching
     */
    public function getActiveListings(array $filters = [])
    {
        $cacheKey = 'listings:active:' . md5(json_encode($filters));
        
        return Cache::remember($cacheKey, self::TTL, function () use ($filters) {
            $query = Listing::active()->withRelations();
            
            if (isset($filters['type'])) {
                $query->where('type', $filters['type']);
            }
            
            return $query->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();
        });
    }

    /**
     * Get user statistics with caching
     */
    public function getUserStatistics(User $user)
    {
        $cacheKey = "user:stats:{$user->id}";
        
        return Cache::remember($cacheKey, self::TTL, function () use ($user) {
            if ($user->role === 'seller') {
                return [
                    'total_listings' => Listing::forSeller($user->id)->count(),
                    'active_listings' => Listing::forSeller($user->id)->active()->count(),
                ];
            }
            
            return [];
        });
    }

    /**
     * Clear user statistics cache
     */
    public function clearUserStatistics(User $user)
    {
        Cache::forget("user:stats:{$user->id}");
    }

    /**
     * Clear active listings cache
     */
    public function clearActiveListings()
    {
        // Clear all listings cache keys (simplified approach)
        Cache::flush(); // In production, use tags or more specific keys
    }

    /**
     * Get listing with caching
     */
    public function getListing($listingId)
    {
        $cacheKey = "listing:{$listingId}";
        
        return Cache::remember($cacheKey, self::TTL, function () use ($listingId) {
            return Listing::with(['seller', 'images', 'bids.user', 'shippingMethods'])
                ->find($listingId);
        });
    }

    /**
     * Clear listing cache
     */
    public function clearListing($listingId)
    {
        Cache::forget("listing:{$listingId}");
    }
}
