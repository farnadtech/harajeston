<?php

namespace App\Services;

use App\Models\Listing;
use App\Models\User;
use App\Exceptions\DirectSale\OutOfStockException;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ListingService
{
    /**
     * Create auction listing
     * 
     * @param User $seller
     * @param array $data
     * @return Listing
     * @throws \InvalidArgumentException
     */
    public function createListing(User $seller, array $data): Listing
    {
        // Validate auction times
        if (!isset($data['starts_at']) || !isset($data['ends_at'])) {
            throw new \InvalidArgumentException('زمان شروع و پایان حراجی الزامی است.');
        }
        
        $this->validateAuctionTimes($data['starts_at'], $data['ends_at']);
        
        // Check if listing requires admin approval
        $requiresApproval = \App\Models\SiteSetting::get('require_listing_approval', false);
        
        // Determine initial status
        $startsAt = Carbon::parse($data['starts_at']);
        
        if (isset($data['seller_id'])) {
            // Admin is creating, use provided status or default to pending
            $status = $data['status'] ?? 'pending';
        } elseif ($requiresApproval) {
            // Seller is creating and approval is required
            $status = 'pending';
        } else {
            // No approval required, set based on start time
            $status = $startsAt->isFuture() ? 'pending' : 'active';
        }
        
        $listing = Listing::create([
            'seller_id' => $data['seller_id'] ?? $seller->id,
            'title' => $data['title'],
            'slug' => $this->generateUniqueSlug($data['title']),
            'description' => $data['description'],
            'category_id' => $data['category_id'] ?? null,
            'condition' => $data['condition'] ?? 'used',
            'starting_price' => $data['starting_price'],
            'current_price' => $data['starting_price'],
            'buy_now_price' => $data['buy_now_price'] ?? null,
            'deposit_amount' => $data['deposit_amount'] ?? 0,
            'bid_increment' => $data['bid_increment'] ?? 10000,
            'starts_at' => $startsAt,
            'ends_at' => Carbon::parse($data['ends_at']),
            'auto_extend' => $data['auto_extend'] ?? false,
            'status' => $status,
            'tags' => isset($data['tags']) ? $this->processTags($data['tags']) : null,
        ]);

        // ذخیره ویژگی‌ها
        if (isset($data['attributes']) && is_array($data['attributes'])) {
            foreach ($data['attributes'] as $attributeId => $value) {
                if (!empty($value)) {
                    $listing->attributeValues()->create([
                        'category_attribute_id' => $attributeId,
                        'value' => $value,
                    ]);
                }
            }
        }

        // ذخیره روش‌های ارسال
        if (isset($data['shipping_methods']) && is_array($data['shipping_methods'])) {
            foreach ($data['shipping_methods'] as $methodId) {
                $customCost = $data['shipping_costs'][$methodId] ?? null;
                $listing->shippingMethods()->attach($methodId, [
                    'custom_cost_adjustment' => $customCost
                ]);
            }
        }

        // ذخیره تصاویر
        if (isset($data['images']) && is_array($data['images'])) {
            $imageService = app(ImageService::class);
            foreach ($data['images'] as $index => $image) {
                // Use upload method instead of store
                $imageService->upload($listing, $image, $index);
            }
        }

        return $listing;
    }

    /**
     * Update existing listing
     */
    public function updateListing(Listing $listing, array $data): Listing
    {
        // Validate auction times
        if (isset($data['starts_at']) && isset($data['ends_at'])) {
            $this->validateAuctionTimes($data['starts_at'], $data['ends_at']);
        }

        // Update basic fields
        $listing->update([
            'title' => $data['title'] ?? $listing->title,
            'description' => $data['description'] ?? $listing->description,
            'category_id' => $data['category_id'] ?? $listing->category_id,
            'condition' => $data['condition'] ?? $listing->condition,
            'starting_price' => $data['starting_price'] ?? $listing->starting_price,
            'buy_now_price' => $data['buy_now_price'] ?? $listing->buy_now_price,
            'deposit_amount' => $data['deposit_amount'] ?? $listing->deposit_amount,
            'bid_increment' => $data['bid_increment'] ?? $listing->bid_increment,
            'starts_at' => isset($data['starts_at']) ? Carbon::parse($data['starts_at']) : $listing->starts_at,
            'ends_at' => isset($data['ends_at']) ? Carbon::parse($data['ends_at']) : $listing->ends_at,
            'auto_extend' => $data['auto_extend'] ?? $listing->auto_extend,
            'tags' => isset($data['tags']) ? (is_array($data['tags']) ? $data['tags'] : $this->processTagsToArray($data['tags'])) : $listing->tags,
        ]);

        // Update attributes
        if (isset($data['attributes']) && is_array($data['attributes'])) {
            // Delete old attributes
            $listing->attributeValues()->delete();
            
            // Add new attributes
            foreach ($data['attributes'] as $attributeId => $value) {
                if (!empty($value)) {
                    $listing->attributeValues()->create([
                        'category_attribute_id' => $attributeId,
                        'value' => $value,
                    ]);
                }
            }
        }

        // Update shipping methods
        if (isset($data['shipping_methods']) && is_array($data['shipping_methods'])) {
            // Detach all old shipping methods
            $listing->shippingMethods()->detach();
            
            // Attach new shipping methods
            foreach ($data['shipping_methods'] as $methodId) {
                $customCost = $data['shipping_costs'][$methodId] ?? null;
                
                // If no custom cost provided, use 0 (will use base cost in display)
                if ($customCost === null || $customCost === '') {
                    $customCost = 0;
                }
                
                $listing->shippingMethods()->attach($methodId, [
                    'custom_cost_adjustment' => $customCost
                ]);
            }
        }

        // Handle new images (existing images are kept unless deleted separately)
        if (isset($data['images']) && is_array($data['images'])) {
            $imageService = app(ImageService::class);
            $currentImageCount = $listing->images()->count();
            
            foreach ($data['images'] as $index => $image) {
                $imageService->upload($listing, $image, $currentImageCount + $index);
            }
        }

        return $listing->fresh();
    }

    /**
     * Process tags string into JSON array
     * 
     * @param string $tagsString
     * @return string|null
     */
    protected function processTags(string $tagsString): ?string
    {
        $tags = array_map('trim', explode(',', $tagsString));
        $tags = array_filter($tags); // Remove empty values
        $tags = array_slice($tags, 0, 5); // Max 5 tags
        
        return !empty($tags) ? json_encode($tags, JSON_UNESCAPED_UNICODE) : null;
    }

    /**
     * Process tags string into array (for update with cast)
     * 
     * @param string $tagsString
     * @return array|null
     */
    protected function processTagsToArray(string $tagsString): ?array
    {
        $tags = array_map('trim', explode(',', $tagsString));
        $tags = array_filter($tags); // Remove empty values
        $tags = array_slice($tags, 0, 5); // Max 5 tags
        
        return !empty($tags) ? array_values($tags) : null;
    }

    /**
     * Validate auction start and end times
     * 
     * @param string|Carbon $startTime
     * @param string|Carbon $endTime
     * @throws \InvalidArgumentException
     */
    protected function validateAuctionTimes($startTime, $endTime): void
    {
        $start = $startTime instanceof Carbon ? $startTime : Carbon::parse($startTime);
        $end = $endTime instanceof Carbon ? $endTime : Carbon::parse($endTime);
        
        if ($end->lte($start)) {
            throw new \InvalidArgumentException('زمان پایان باید بعد از زمان شروع باشد.');
        }
        
        // Allow past start time for admin (they might be creating backdated auctions)
        // For sellers, start time must be in future (validated in request)
    }


    /**
     * Generate unique slug from title
     * 
     * @param string $title
     * @return string
     */
    protected function generateUniqueSlug(string $title): string
    {
        $slug = \Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;
        
        while (Listing::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
}
