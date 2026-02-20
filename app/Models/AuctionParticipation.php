<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuctionParticipation extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id',
        'user_id',
        'deposit_amount',
        'deposit_status',
    ];

    protected $casts = [
        'deposit_amount' => 'integer',
    ];

    // Relationships
    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Status check methods
    public function isFrozen(): bool
    {
        return $this->deposit_status === 'frozen';
    }

    public function isReleased(): bool
    {
        return $this->deposit_status === 'released';
    }

    public function isForfeited(): bool
    {
        return $this->deposit_status === 'forfeited';
    }

    public function isApplied(): bool
    {
        return $this->deposit_status === 'applied';
    }
}
