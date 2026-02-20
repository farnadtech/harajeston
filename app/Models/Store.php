<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'store_name',
        'slug',
        'description',
        'banner_image',
        'logo_image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class, 'seller_id', 'user_id');
    }

    // Accessors for banner/logo URLs
    public function getBannerUrlAttribute(): ?string
    {
        return $this->banner_image 
            ? Storage::url($this->banner_image) 
            : null;
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_image 
            ? Storage::url($this->logo_image) 
            : null;
    }

    // Slug generation method
    public static function generateUniqueSlug(string $username): string
    {
        $slug = Str::slug($username);
        $originalSlug = $slug;
        $counter = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        return $slug;
    }

    // Scope for active stores
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
