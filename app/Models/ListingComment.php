<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ListingComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id',
        'user_id',
        'parent_id',
        'type',
        'content',
        'rating',
        'status',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ListingComment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(ListingComment::class, 'parent_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function approve(User $admin): void
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $admin->id,
        ]);
    }

    public function reject(): void
    {
        $this->update(['status' => 'rejected']);
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isComment(): bool
    {
        return $this->type === 'comment';
    }

    public function isQuestion(): bool
    {
        return $this->type === 'question';
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeComments($query)
    {
        return $query->where('type', 'comment');
    }

    public function scopeQuestions($query)
    {
        return $query->where('type', 'question');
    }

    public function scopeParentOnly($query)
    {
        return $query->whereNull('parent_id');
    }
    
    public function scopeWithRating($query)
    {
        return $query->whereNotNull('rating');
    }
    
    public function hasRating(): bool
    {
        return $this->rating !== null;
    }
}
