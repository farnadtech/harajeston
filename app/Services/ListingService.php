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
        
        // Determine initial status - SIMPLIFIED WORKFLOW
        $startsAt = Carbon::parse($data['starts_at']);
        
        if (isset($data['seller_id'])) {
            // Admin is creating, use provided status or default to pending
            $status = $data['status'] ?? 'pending';
        } elseif ($requiresApproval) {
            // Seller is creating and approval is required - always pending
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
            'bid_increment' => \App\Models\SiteSetting::get('default_bid_increment', 10000),
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
        // Check if listing requires approval
        $requiresApproval = \App\Models\SiteSetting::get('require_listing_approval', false);
        
        // Check if listing has active bids
        $hasActiveBids = $listing->hasActiveBids();
        
        // If listing is active and has bids, only allow description and shipping changes
        if ($listing->status === 'active' && $hasActiveBids) {
            // Only update description and shipping methods
            $listing->update([
                'description' => $data['description'] ?? $listing->description,
            ]);
            
            // Update shipping methods
            if (isset($data['shipping_methods']) && is_array($data['shipping_methods'])) {
                $listing->shippingMethods()->detach();
                
                foreach ($data['shipping_methods'] as $methodId) {
                    $customCost = $data['shipping_costs'][$methodId] ?? null;
                    if ($customCost === null || $customCost === '') {
                        $customCost = 0;
                    }
                    $listing->shippingMethods()->attach($methodId, [
                        'custom_cost_adjustment' => $customCost
                    ]);
                }
            }
            
            return $listing->fresh();
        }
        
        // If listing is active/pending and approval is required, save as pending changes
        if (($listing->status === 'active' || $listing->status === 'pending') && $requiresApproval && !auth()->user()->isAdmin()) {
            // Handle new images first (upload them)
            $uploadedImages = [];
            if (isset($data['images']) && is_array($data['images'])) {
                $imageService = app(ImageService::class);
                $currentImageCount = $listing->images()->count();
                
                foreach ($data['images'] as $index => $image) {
                    $uploadedImage = $imageService->upload($listing, $image, $currentImageCount + $index);
                    $uploadedImages[] = [
                        'id' => $uploadedImage->id,
                        'file_path' => $uploadedImage->file_path,
                        'file_name' => $uploadedImage->file_name,
                    ];
                }
            }
            
            // Replace file uploads with image info in data
            if (!empty($uploadedImages)) {
                $data['images'] = $uploadedImages;
            } else {
                unset($data['images']);
            }
            
            // Save changes to pending_changes table
            \App\Models\ListingPendingChange::create([
                'listing_id' => $listing->id,
                'changes' => $data,
                'status' => 'pending',
            ]);
            
            // Don't update the listing itself
            return $listing;
        }

        // Validate auction times
        if (isset($data['starts_at']) && isset($data['ends_at'])) {
            $this->validateAuctionTimes($data['starts_at'], $data['ends_at']);
        }

        // Determine new status
        $newStatus = $listing->status;
        
        // If suspended, require re-approval
        if ($listing->status === 'suspended') {
            $newStatus = 'pending';
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
            'starts_at' => isset($data['starts_at']) ? Carbon::parse($data['starts_at']) : $listing->starts_at,
            'ends_at' => isset($data['ends_at']) ? Carbon::parse($data['ends_at']) : $listing->ends_at,
            'auto_extend' => $data['auto_extend'] ?? $listing->auto_extend,
            'tags' => isset($data['tags']) ? (is_array($data['tags']) ? $data['tags'] : $this->processTagsToArray($data['tags'])) : $listing->tags,
            'status' => $newStatus,
        ]);

        // Update attributes
        if (isset($data['attributes']) && is_array($data['attributes'])) {
            $listing->attributeValues()->delete();
            
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
            $listing->shippingMethods()->detach();
            
            foreach ($data['shipping_methods'] as $methodId) {
                $customCost = $data['shipping_costs'][$methodId] ?? null;
                
                if ($customCost === null || $customCost === '') {
                    $customCost = 0;
                }
                
                $listing->shippingMethods()->attach($methodId, [
                    'custom_cost_adjustment' => $customCost
                ]);
            }
        }

        // Handle deleted images
        if (isset($data['deleted_images']) && !empty($data['deleted_images'])) {
            $deletedIds = explode(',', $data['deleted_images']);
            $imageService = app(ImageService::class);
            
            foreach ($deletedIds as $imageId) {
                $image = $listing->images()->find($imageId);
                if ($image) {
                    $imageService->delete($image, true);
                }
            }
        }

        // Handle new images
        if (isset($data['images']) && is_array($data['images'])) {
            $imageService = app(ImageService::class);
            $currentImageCount = $listing->images()->count();
            
            foreach ($data['images'] as $index => $image) {
                $imageService->upload($listing, $image, $currentImageCount + $index);
            }
        }

        // Update main image
        if (isset($data['main_image_id']) && !empty($data['main_image_id'])) {
            $mainImage = $listing->images()->find($data['main_image_id']);
            if ($mainImage) {
                $mainImage->update(['display_order' => 0]);
                
                $listing->images()
                    ->where('id', '!=', $data['main_image_id'])
                    ->orderBy('display_order')
                    ->get()
                    ->each(function ($image, $index) {
                        $image->update(['display_order' => $index + 1]);
                    });
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
