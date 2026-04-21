<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'creator_id',
        'recipient_id',
        'listing_id',
        'subject',
        'type',
        'status',
        'priority',
        'last_reply_at',
    ];

    protected $casts = [
        'last_reply_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = 'TKT-' . strtoupper(substr(uniqid(), -8));
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class)->orderBy('created_at');
    }

    public function latestMessage(): HasMany
    {
        return $this->hasMany(TicketMessage::class)->latest()->limit(1);
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'open'     => 'باز',
            'answered' => 'پاسخ داده شده',
            'closed'   => 'بسته شده',
            default    => $this->status,
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'buyer_to_seller'  => 'خریدار به فروشنده',
            'buyer_to_admin'   => 'خریدار به ادمین',
            'seller_to_buyer'  => 'فروشنده به خریدار',
            'seller_to_admin'  => 'فروشنده به ادمین',
            'admin_to_buyer'   => 'ادمین به خریدار',
            'admin_to_seller'  => 'ادمین به فروشنده',
            default            => $this->type,
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return match($this->priority) {
            'low'    => 'کم',
            'normal' => 'معمولی',
            'high'   => 'زیاد',
            default  => $this->priority,
        };
    }

    /**
     * تعداد پیام‌های خوانده نشده برای یک کاربر خاص
     */
    public function unreadCountFor(int $userId): int
    {
        return $this->messages()->where('user_id', '!=', $userId)->where('is_read', false)->count();
    }
}
