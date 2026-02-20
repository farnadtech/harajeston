<?php

use App\Services\PersianNumberService;

if (!function_exists('fa_number')) {
    /**
     * Convert number to Persian digits
     *
     * @param mixed $number
     * @return string
     */
    function fa_number($number): string
    {
        $service = app(PersianNumberService::class);
        return $service->toPersian($number);
    }
}

if (!function_exists('fa_price')) {
    /**
     * Format price with Persian digits and thousand separators
     *
     * @param mixed $amount
     * @return string
     */
    function fa_price($amount): string
    {
        if (is_null($amount)) {
            return fa_number(0);
        }
        
        $service = app(PersianNumberService::class);
        return $service->formatNumber($amount, true);
    }
}

if (!function_exists('en_number')) {
    /**
     * Convert Persian digits to English
     *
     * @param string $number
     * @return string
     */
    function en_number(string $number): string
    {
        $service = app(PersianNumberService::class);
        return $service->toEnglish($number);
    }
}

if (!function_exists('time_remaining')) {
    /**
     * Get time remaining in Persian format
     *
     * @param \Carbon\Carbon $endTime
     * @return string
     */
    function time_remaining($endTime): string
    {
        if (!$endTime) {
            return 'نامشخص';
        }

        $now = \Carbon\Carbon::now();
        
        if ($now->greaterThanOrEqualTo($endTime)) {
            return 'پایان یافته';
        }

        $diff = $now->diff($endTime);
        
        $days = $diff->d;
        $hours = $diff->h;
        $minutes = $diff->i;

        if ($days > 0) {
            return \App\Services\PersianNumberService::convertToPersian($days) . ' روز';
        } elseif ($hours > 0) {
            return \App\Services\PersianNumberService::convertToPersian($hours) . ' ساعت';
        } elseif ($minutes > 0) {
            return \App\Services\PersianNumberService::convertToPersian($minutes) . ' دقیقه';
        } else {
            return 'کمتر از یک دقیقه';
        }
    }
}

if (!function_exists('condition_label')) {
    /**
     * Get Persian label for listing condition
     *
     * @param string|null $condition
     * @return string
     */
    function condition_label(?string $condition): string
    {
        return match($condition) {
            'new' => 'نو',
            'like_new' => 'در حد نو',
            'used' => 'دست دوم',
            default => 'نو'
        };
    }
}
