<?php

namespace App\Services;

class PersianNumberService
{
    /**
     * Persian digits
     */
    const PERSIAN_DIGITS = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    
    /**
     * English digits
     */
    const ENGLISH_DIGITS = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    /**
     * Convert English numbers to Persian
     */
    public static function toPersian($number): string
    {
        return str_replace(self::ENGLISH_DIGITS, self::PERSIAN_DIGITS, (string) $number);
    }

    /**
     * Alias for toPersian
     */
    public static function convertToPersian($number): string
    {
        return self::toPersian($number);
    }

    /**
     * Convert Persian numbers to English
     */
    public static function toEnglish(string $number): string
    {
        return str_replace(self::PERSIAN_DIGITS, self::ENGLISH_DIGITS, $number);
    }

    /**
     * Format number as currency (Rial)
     */
    public static function formatCurrency($amount, bool $usePersianDigits = true): string
    {
        // Format with thousand separators
        $formatted = number_format($amount, 0, '.', ',');
        
        // Convert to Persian digits if requested
        if ($usePersianDigits) {
            $formatted = self::toPersian($formatted);
        }
        
        return $formatted . ' ریال';
    }

    /**
     * Format number with thousand separators
     */
    public static function formatNumber($number, bool $usePersianDigits = true): string
    {
        $formatted = number_format($number, 0, '.', ',');
        
        if ($usePersianDigits) {
            $formatted = self::toPersian($formatted);
        }
        
        return $formatted;
    }

    /**
     * Format decimal number
     */
    public static function formatDecimal($number, int $decimals = 2, bool $usePersianDigits = true): string
    {
        $formatted = number_format($number, $decimals, '.', ',');
        
        if ($usePersianDigits) {
            $formatted = self::toPersian($formatted);
        }
        
        return $formatted;
    }
}
