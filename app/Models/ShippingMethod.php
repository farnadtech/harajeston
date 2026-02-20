<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ShippingMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'base_cost',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'base_cost' => 'integer',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function listings(): BelongsToMany
    {
        return $this->belongsToMany(Listing::class, 'listing_shipping')
            ->withPivot('custom_cost_adjustment')
            ->withTimestamps();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scope for active methods
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Calculate total cost for a listing
    public function calculateCostForListing(Listing $listing): float
    {
        $pivot = $listing->shippingMethods()
            ->where('shipping_method_id', $this->id)
            ->first();

        if (!$pivot) {
            return (float) $this->base_cost;
        }

        $adjustment = $pivot->pivot->custom_cost_adjustment ?? 0;
        return (float) ($this->base_cost + $adjustment);
    }
}
