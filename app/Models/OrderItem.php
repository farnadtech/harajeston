<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'listing_id',
        'quantity',
        'price_snapshot',
        'subtotal',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price_snapshot' => 'integer',
        'subtotal' => 'integer',
    ];

    public $timestamps = true;
    const UPDATED_AT = null; // Only use created_at

    // Relationships
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    // Calculate subtotal if not set
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($orderItem) {
            if (!$orderItem->subtotal) {
                $orderItem->subtotal = $orderItem->price_snapshot * $orderItem->quantity;
            }
        });
    }
}
