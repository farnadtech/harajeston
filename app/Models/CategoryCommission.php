<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoryCommission extends Model
{
    protected $fillable = [
        'category_id',
        'type',
        'fixed_amount',
        'percentage',
    ];

    protected $casts = [
        'fixed_amount' => 'decimal:2',
        'percentage' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
