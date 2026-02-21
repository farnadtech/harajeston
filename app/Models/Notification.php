<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'icon',
        'color',
        'link',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
    ];

    protected $appends = ['time_ago'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead(): void
    {
        $this->update(['is_read' => true]);
    }

    public function getTimeAgoAttribute(): string
    {
        $diff = $this->created_at->diffInMinutes(now());
        
        if ($diff < 1) {
            return 'همین الان';
        } elseif ($diff < 60) {
            return $diff . ' دقیقه پیش';
        } elseif ($diff < 1440) {
            return floor($diff / 60) . ' ساعت پیش';
        } else {
            return floor($diff / 1440) . ' روز پیش';
        }
    }
}
