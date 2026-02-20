<?php

namespace App\Services;

use Morilog\Jalali\Jalalian;
use Carbon\Carbon;

class JalaliDateService
{
    /**
     * Convert Gregorian date to Jalali
     */
    public static function toJalali($date, string $format = 'Y/m/d'): string
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }
        
        return Jalalian::fromCarbon($date)->format($format);
    }

    /**
     * Convert Jalali date to Gregorian
     */
    public static function toGregorian(string $jalaliDate): Carbon
    {
        // Parse Jalali date (format: Y/m/d or Y-m-d)
        $parts = preg_split('/[-\/]/', $jalaliDate);
        
        if (count($parts) !== 3) {
            throw new \InvalidArgumentException('فرمت تاریخ نامعتبر است. فرمت صحیح: Y/m/d یا Y-m-d');
        }
        
        [$year, $month, $day] = $parts;
        
        $jalalian = Jalalian::fromFormat('Y/m/d', "$year/$month/$day");
        return $jalalian->toCarbon();
    }

    /**
     * Format date with time
     */
    public static function toJalaliDateTime($date): string
    {
        return self::toJalali($date, 'Y/m/d H:i:s');
    }

    /**
     * Get Jalali month name
     */
    public static function getMonthName(int $month): string
    {
        $months = [
            1 => 'فروردین',
            2 => 'اردیبهشت',
            3 => 'خرداد',
            4 => 'تیر',
            5 => 'مرداد',
            6 => 'شهریور',
            7 => 'مهر',
            8 => 'آبان',
            9 => 'آذر',
            10 => 'دی',
            11 => 'بهمن',
            12 => 'اسفند',
        ];
        
        return $months[$month] ?? '';
    }

    /**
     * Get Jalali day name
     */
    public static function getDayName($date): string
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }
        
        $dayOfWeek = $date->dayOfWeek;
        
        $days = [
            0 => 'یکشنبه',
            1 => 'دوشنبه',
            2 => 'سه‌شنبه',
            3 => 'چهارشنبه',
            4 => 'پنج‌شنبه',
            5 => 'جمعه',
            6 => 'شنبه',
        ];
        
        return $days[$dayOfWeek] ?? '';
    }

    /**
     * Format date in human-readable Jalali format
     */
    public static function toHumanReadable($date): string
    {
        if (is_string($date)) {
            $date = Carbon::parse($date);
        }
        
        $jalalian = Jalalian::fromCarbon($date);
        $dayName = self::getDayName($date);
        $monthName = self::getMonthName($jalalian->getMonth());
        
        return sprintf(
            '%s، %d %s %d',
            $dayName,
            $jalalian->getDay(),
            $monthName,
            $jalalian->getYear()
        );
    }
}
