<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'balance',
        'frozen',
    ];

    protected $casts = [
        'balance' => 'integer',
        'frozen' => 'integer',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    // Computed attributes
    public function getAvailableBalanceAttribute(): float
    {
        return (float) $this->balance;
    }

    public function getTotalBalanceAttribute(): float
    {
        return (float) ($this->balance + $this->frozen);
    }
}
