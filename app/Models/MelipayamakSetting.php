<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MelipayamakSetting extends Model
{
    protected $table = 'melipayamak_settings';

    protected $fillable = ['username', 'password', 'api_key', 'from_number', 'body_id', 'is_active'];

    protected $hidden = ['password', 'api_key'];

    protected $casts = ['is_active' => 'boolean'];

    public static function get(): ?self
    {
        return static::first();
    }

    public function isConfigured(): bool
    {
        return !empty($this->username) && (!empty($this->password) || !empty($this->api_key));
    }

    /**
     * مقدار رمز عبور یا ApiKey برای ارسال به API
     * اگر ApiKey موجود باشد، اولویت دارد
     */
    public function getAuthPassword(): string
    {
        return !empty($this->api_key) ? $this->api_key : ($this->password ?? '');
    }
}
