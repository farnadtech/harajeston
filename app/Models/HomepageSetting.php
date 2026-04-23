<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class HomepageSetting extends Model
{
    protected $table = 'homepage_settings';
    protected $fillable = ['key', 'value'];

    /**
     * دریافت یک تنظیم با مقدار پیش‌فرض
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember("homepage_setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * ذخیره یک تنظیم
     */
    public static function set(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("homepage_setting_{$key}");
    }

    /**
     * دریافت همه تنظیمات به صورت آرایه
     */
    public static function getAllSettings(): array
    {
        return Cache::remember('homepage_settings_all', 3600, function () {
            return static::all()->pluck('value', 'key')->toArray();
        });
    }

    /**
     * پاک کردن کش همه تنظیمات
     */
    public static function clearCache(): void
    {
        Cache::forget('homepage_settings_all');
        static::all()->each(fn($s) => Cache::forget("homepage_setting_{$s->key}"));
    }
}
