<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AdminActionLog extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'admin_id',
        'listing_id',
        'action',
        'description',
        'icon',
        'target_type',
        'target_id',
        'context',
        'reason',
        'ip_address',
    ];
    
    protected $casts = [
        'context' => 'array',
    ];
    
    /**
     * Get the admin who performed the action
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
    
    /**
     * Get the target model (polymorphic)
     */
    public function target(): MorphTo
    {
        return $this->morphTo();
    }
    
    /**
     * Get the listing
     */
    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }
}
