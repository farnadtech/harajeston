<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'wallet_id',
        'type',
        'amount',
        'tax_amount',
        'final_amount',
        'gateway',
        'transaction_id',
        'reference_id',
        'status',
        'description',
        'balance_before',
        'balance_after',
        'frozen_before',
        'frozen_after',
        'reference_type',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function user()
    {
        return $this->wallet->user();
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCharges($query)
    {
        return $query->where('type', 'deposit');
    }
}
