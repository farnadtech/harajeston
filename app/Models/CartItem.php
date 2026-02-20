<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'listing_id',
        'quantity',
        'price_snapshot',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price_snapshot' => 'integer',
    ];

    // Relationships
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    // Calculate item subtotal
    public function getSubtotalAttribute(): float
    {
        return (float) ($this->price_snapshot * $this->quantity);
    }

    // Check if listing is still available
    public function isAvailable(): bool
    {
        return $this->listing 
            && $this->listing->isDirectSale() 
            && $this->listing->stock >= $this->quantity;
    }
}
