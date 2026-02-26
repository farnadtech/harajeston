<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Listing extends Model
{
    use HasFactory;

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected $fillable = [
        'seller_id',
        'title',
        'slug',
        'description',
        'category_id',
        'condition',
        'tags',
        // همه محصولات حراج هستند
        'starting_price',
        'current_price',
        'buy_now_price', // قیمت خرید فوری (اختیاری)
        'required_deposit',
        'reserve_price',
        'bid_increment',
        'deposit_amount',
        'highest_bidder_id',
        'current_winner_id',
        'starts_at',
        'ends_at',
        'finalization_deadline',
        'status',
        'suspension_reason',
        'rejection_reason',
        'approved_at',
        'approved_by',
        'auto_extend',
        'show_before_start',
        'views',
        'favorites',
        'shares',
    ];

    protected $casts = [
        'starting_price' => 'integer',
        'current_price' => 'integer',
        'buy_now_price' => 'integer',
        'required_deposit' => 'integer',
        'reserve_price' => 'integer',
        'bid_increment' => 'integer',
        'deposit_amount' => 'integer',
        'starts_at' => 'datetime',
        'show_before_start' => 'boolean',
        'ends_at' => 'datetime',
        'finalization_deadline' => 'datetime',
        'tags' => 'array',
        'auto_extend' => 'boolean',
        'views' => 'integer',
        'favorites' => 'integer',
        'shares' => 'integer',
    ];

    /**
     * Boot the model and add event listeners
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug when creating a listing if not provided
        static::creating(function ($listing) {
            if (empty($listing->slug)) {
                $slug = \Str::slug($listing->title);
                $originalSlug = $slug;
                $counter = 1;

                // Ensure slug is unique
                while (static::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }

                $listing->slug = $slug;
            }
        });
    }

    // Relationships
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'seller_id', 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function highestBidder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'highest_bidder_id');
    }

    public function currentWinner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'current_winner_id');
    }

    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }

    public function participations(): HasMany
    {
        return $this->hasMany(AuctionParticipation::class);
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'auction_participations', 'listing_id', 'user_id')
            ->withPivot('deposit_paid', 'deposit_returned', 'participated_at')
            ->withTimestamps();
    }

    public function images(): HasMany
    {
        return $this->hasMany(ListingImage::class)->orderBy('display_order');
    }

    public function shippingMethods(): BelongsToMany
    {
        return $this->belongsToMany(ShippingMethod::class, 'listing_shipping')
            ->withPivot('custom_cost_adjustment')
            ->withTimestamps();
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function attributeValues(): HasMany
    {
        return $this->hasMany(ListingAttributeValue::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ListingComment::class);
    }

    public function pendingChanges(): HasMany
    {
        return $this->hasMany(ListingPendingChange::class);
    }

    public function hasPendingChanges(): bool
    {
        return $this->pendingChanges()->where('status', 'pending')->exists();
    }

    // Helper methods
    public function isActive(): bool
    {
        return $this->status === 'active' 
            && now()->between($this->starts_at, $this->ends_at);
    }

    public function hasStarted(): bool
    {
        return $this->starts_at && now()->greaterThanOrEqualTo($this->starts_at);
    }

    public function hasEnded(): bool
    {
        return $this->ends_at && now()->greaterThanOrEqualTo($this->ends_at);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending' || ($this->status === 'active' && !$this->hasStarted());
    }

    public function hasBuyNowPrice(): bool
    {
        return $this->buy_now_price !== null && $this->buy_now_price > 0;
    }

    public function canBuyNow(): bool
    {
        return $this->hasBuyNowPrice() 
            && $this->isActive() 
            && $this->status === 'active';
    }
    
    /**
     * Update listing rating based on approved comments with ratings
     */
    public function updateRating(): void
    {
        $ratings = $this->comments()
            ->approved()
            ->comments()
            ->whereNotNull('rating')
            ->whereNull('parent_id')
            ->pluck('rating');
        
        if ($ratings->isEmpty()) {
            $this->update([
                'average_rating' => 0,
                'rating_count' => 0,
            ]);
            return;
        }
        
        $this->update([
            'average_rating' => round($ratings->avg(), 2),
            'rating_count' => $ratings->count(),
        ]);
    }

    // Status scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeEnded($query)
    {
        return $query->where('status', 'ended');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Performance optimization scopes
    public function scopeWithRelations($query)
    {
        return $query->with(['seller', 'images', 'shippingMethods']);
    }

    public function scopeForSeller($query, $sellerId)
    {
        return $query->where('seller_id', $sellerId);
    }

    public function scopeEndingSoon($query, $hours = 24)
    {
        return $query->where('status', 'active')
            ->where('ends_at', '<=', now()->addHours($hours))
            ->where('ends_at', '>', now());
    }

    public function scopeWithBuyNow($query)
    {
        return $query->whereNotNull('buy_now_price')
            ->where('buy_now_price', '>', 0);
    }

    /**
     * Check if listing has active bids
     */
    public function hasActiveBids(): bool
    {
        return $this->bids()->exists();
    }
}
