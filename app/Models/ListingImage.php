<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ListingImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id',
        'file_path',
        'file_name',
        'display_order',
    ];

    protected $casts = [
        'display_order' => 'integer',
    ];

    public $timestamps = true;
    const UPDATED_AT = null; // Only use created_at

    // Relationships
    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    // Accessor for full URL
    public function getUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    // Scope for ordering
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }
}
