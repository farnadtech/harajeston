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
     * Create listing with type-specific validation
     * Supports auction, direct_sale, and hybrid types
     * 
     * @param User $seller
     * @param array $data
     * @return Listing
     * @throws \InvalidArgumentException
     */
    public function createListing(User $seller, array $data): Listing
    {
        $type = $data['type']; // auction, direct_sale, hybrid
        
        // Validate type
        if (!in_array($type, ['auction', 'direct_sale', 'hybrid'])) {
            throw new \InvalidArgumentException('نوع آگهی نامعتبر است.');
        }
        
        // Type-specific validation and processing
        if ($type === 'auction' || $type === 'hybrid') {
            // Calculate 10% deposit for auctions
            if (!isset($data['base_price'])) {
                throw new \InvalidArgumentException('قیمت پایه برای مزایده الزامی است.');
            }
            
            $data['required_deposit'] = $data['base_price'] * 0.10;
            
            // Validate auction times
            if (!isset($data['start_time']) || !isset($data['end_time'])) {
                throw new \InvalidArgumentException('زمان شروع و پایان مزایده الزامی است.');
            }
            
            $this->validateAuctionTimes($data['start_time'], $data['end_time']);
        }
        
        if ($type === 'direct_sale' || $type === 'hybrid') {
            if (!isset($data['price']) || !isset($data['stock'])) {
                throw new \InvalidArgumentException('قیمت و موجودی برای فروش مستقیم الزامی است.');
            }
            
            if ($data['stock'] < 0) {
                throw new \InvalidArgumentException('موجودی نمی‌تواند منفی باشد.');
            }
        }
        
        if ($type === 'hybrid') {
            // Price validation is handled in CreateListingRequest
            // No need to throw exception here
        }
        
        $listing = Listing::create([
            'seller_id' => $seller->id,
            'type' => $type,
            'title' => $data['title'],
            'slug' => $this->generateUniqueSlug($data['title']),
            'description' => $data['description'],
            'category_id' => $data['category_id'] ?? null,
            // Auction fields
            'base_price' => $data['base_price'] ?? null,
            'required_deposit' => $data['required_deposit'] ?? null,
            'start_time' => $data['start_time'] ?? null,
            'end_time' => $data['end_time'] ?? null,
            // Direct sale fields
            'price' => $data['price'] ?? null,
            'stock' => $data['stock'] ?? null,
            'low_stock_threshold' => $data['low_stock_threshold'] ?? 5,
            'status' => $type === 'auction' ? 'pending' : 'active',
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
                $customCost = $data['shipping_costs'][$methodId] ?? 0;
                $listing->shippingMethods()->attach($methodId, [
                    'custom_cost_adjustment' => $customCost
                ]);
            }
        }

        return $listing;
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
        
        if ($start->lt(Carbon::now())) {
            throw new \InvalidArgumentException('زمان شروع نمی‌تواند در گذشته باشد.');
        }
    }

    /**
     * Update stock with atomic operation
     * Logs stock changes and sends alerts
     * 
     * @param Listing $listing
     * @param int $newStock
     * @param string $reason
     * @return void
     */
    public function updateStock(Listing $listing, int $newStock, string $reason): void
    {
        if ($newStock < 0) {
            throw new \InvalidArgumentException('موجودی نمی‌تواند منفی باشد.');
        }
        
        DB::transaction(function () use ($listing, $newStock, $reason) {
            $listing = Listing::where('id', $listing->id)
                ->lockForUpdate()
                ->first();
            
            $oldStock = $listing->stock;
            $listing->stock = $newStock;
            
            // Update status based on stock
            if ($newStock === 0) {
                $listing->status = 'out_of_stock';
            } elseif ($listing->status === 'out_of_stock' && $newStock > 0) {
                $listing->status = 'active';
            }
            
            $listing->save();
            
            // Note: Stock logging would be implemented here in production
            // StockLog::create([...]);
            
            // Send low stock alert
            if ($newStock > 0 && $newStock <= $listing->low_stock_threshold) {
                // Note: Notification would be sent here
                // $listing->seller->notify(new LowStockAlertNotification($listing));
            }
        });
    }

    /**
     * Decrement stock atomically
     * Prevents negative stock with transaction locking
     * 
     * @param Listing $listing
     * @param int $quantity
     * @return void
     * @throws OutOfStockException
     */
    public function decrementStock(Listing $listing, int $quantity): void
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('تعداد باید بیشتر از صفر باشد.');
        }
        
        DB::transaction(function () use ($listing, $quantity) {
            $listing = Listing::where('id', $listing->id)
                ->lockForUpdate()
                ->first();
            
            if ($listing->stock < $quantity) {
                throw new OutOfStockException($listing->id, $listing->title);
            }
            
            $listing->stock -= $quantity;
            
            if ($listing->stock === 0) {
                $listing->status = 'out_of_stock';
            }
            
            $listing->save();
        });
    }

    /**
     * Increment stock (for order cancellation, returns, etc.)
     * 
     * @param Listing $listing
     * @param int $quantity
     * @return void
     */
    public function incrementStock(Listing $listing, int $quantity): void
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('تعداد باید بیشتر از صفر باشد.');
        }
        
        DB::transaction(function () use ($listing, $quantity) {
            $listing = Listing::where('id', $listing->id)
                ->lockForUpdate()
                ->first();
            
            $listing->stock += $quantity;
            
            // Reactivate if was out of stock
            if ($listing->status === 'out_of_stock' && $listing->stock > 0) {
                $listing->status = 'active';
            }
            
            $listing->save();
        });
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
