<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterSubscriber extends Model
{
    protected $fillable = ['email', 'name', 'is_active', 'unsubscribe_token', 'subscribed_at'];

    protected $casts = ['is_active' => 'boolean', 'subscribed_at' => 'datetime'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->unsubscribe_token) {
                $model->unsubscribe_token = \Illuminate\Support\Str::random(32);
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
