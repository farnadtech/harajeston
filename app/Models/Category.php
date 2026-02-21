<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'parent_id',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    // Relationships
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('order');
    }

    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class, 'category_id');
    }

    public function attributes(): HasMany
    {
        return $this->hasMany(CategoryAttribute::class)->orderBy('order');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    // Helper methods
    public function isParent(): bool
    {
        return $this->parent_id === null;
    }

    public function hasChildren(): bool
    {
        return $this->children()->count() > 0;
    }

    public function getFullPath(): string
    {
        if ($this->parent) {
            return $this->parent->getFullPath() . ' > ' . $this->name;
        }
        return $this->name;
    }

    /**
     * دریافت تمام دسته‌بندی‌های اصلی با زیردسته‌ها (تا سطح سوم)
     */
    public static function getMenuStructure(): array
    {
        $parents = self::active()
            ->parents()
            ->ordered()
            ->with(['children' => function ($query) {
                $query->active()->ordered()->with(['children' => function ($q) {
                    $q->active()->ordered();
                }]);
            }])
            ->get();

        // ساخت دستی آرایه برای حفظ تمام سطوح
        return $parents->map(function ($parent) {
            return [
                'id' => $parent->id,
                'name' => $parent->name,
                'slug' => $parent->slug,
                'icon' => $parent->icon,
                'children' => $parent->children->map(function ($child) {
                    return [
                        'id' => $child->id,
                        'name' => $child->name,
                        'slug' => $child->slug,
                        'icon' => $child->icon,
                        'children' => $child->children->map(function ($grandchild) {
                            return [
                                'id' => $grandchild->id,
                                'name' => $grandchild->name,
                                'slug' => $grandchild->slug,
                                'icon' => $grandchild->icon,
                            ];
                        })->values()->toArray(),
                    ];
                })->values()->toArray(),
            ];
        })->values()->toArray();
    }
}
