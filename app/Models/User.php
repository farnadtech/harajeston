<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'password',
        'role',
        'seller_status',
        'seller_requested_at',
        'seller_approved_at',
        'seller_rejection_reason',
        'seller_request_data',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'seller_requested_at' => 'datetime',
        'seller_approved_at' => 'datetime',
        'seller_request_data' => 'array',
    ];

    // Relationships
    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    public function store(): HasOne
    {
        return $this->hasOne(Store::class);
    }

    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class, 'seller_id');
    }

    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }

    public function participations(): HasMany
    {
        return $this->hasMany(AuctionParticipation::class);
    }

    public function buyerOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    public function sellerOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'seller_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    public function wonAuctions(): HasMany
    {
        return $this->hasMany(Listing::class, 'current_winner_id')
            ->where('status', 'completed')
            ->where('type', 'auction');
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function sellerReviews(): HasMany
    {
        return $this->hasMany(SellerReview::class, 'seller_id');
    }

    public function buyerReviews(): HasMany
    {
        return $this->hasMany(SellerReview::class, 'buyer_id');
    }

    /**
     * Update seller rating based on approved reviews
     */
    public function updateSellerRating(): void
    {
        $ratings = $this->sellerReviews()
            ->approved()
            ->pluck('rating');
        
        if ($ratings->isEmpty()) {
            $this->update([
                'seller_rating' => 0,
                'seller_rating_count' => 0,
            ]);
            return;
        }
        
        $this->update([
            'seller_rating' => round($ratings->avg(), 2),
            'seller_rating_count' => $ratings->count(),
        ]);
    }

    // Role accessor methods
    public function isBuyer(): bool
    {
        return $this->role === 'buyer';
    }

    public function isSeller(): bool
    {
        return $this->role === 'seller';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // Scope methods
    public function scopeBuyers($query)
    {
        return $query->where('role', 'buyer');
    }

    public function scopeSellers($query)
    {
        return $query->where('role', 'seller');
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    // Seller status methods
    public function canSell(): bool
    {
        return $this->seller_status === 'active';
    }

    public function hasRequestedSeller(): bool
    {
        return in_array($this->seller_status, ['pending', 'active', 'suspended']);
    }

    public function isSellerPending(): bool
    {
        return $this->seller_status === 'pending';
    }

    public function isSellerActive(): bool
    {
        return $this->seller_status === 'active';
    }

    public function isSellerSuspended(): bool
    {
        return $this->seller_status === 'suspended';
    }

    public function isSellerRejected(): bool
    {
        return $this->seller_status === 'rejected';
    }

    public function scopeSellerPending($query)
    {
        return $query->where('seller_status', 'pending');
    }

    public function scopeSellerActive($query)
    {
        return $query->where('seller_status', 'active');
    }

    public function scopeSellerSuspended($query)
    {
        return $query->where('seller_status', 'suspended');
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }
}
