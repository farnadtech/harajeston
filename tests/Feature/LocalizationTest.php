<?php

use App\Services\JalaliDateService;
use App\Services\PersianNumberService;
use Carbon\Carbon;

describe('Localization Services', function () {
    test('Property 31: Jalali Date Formatting - dates are correctly converted to Jalali', function () {
        $jalaliService = app(JalaliDateService::class);
        
        // Test known date: 2024-03-20 (1403/01/01 in Jalali)
        $gregorianDate = Carbon::create(2024, 3, 20);
        $jalaliDate = $jalaliService->toJalali($gregorianDate);
        
        expect($jalaliDate)->toBe('1403/01/01');
    });

    test('Property 32: Persian Number Formatting - numbers are correctly converted to Persian digits', function () {
        $persianService = app(PersianNumberService::class);
        
        // Test various numbers
        $testCases = [
            '0' => '۰',
            '1' => '۱',
            '123' => '۱۲۳',
            '1234567890' => '۱۲۳۴۵۶۷۸۹۰',
        ];
        
        foreach ($testCases as $english => $persian) {
            expect($persianService->toPersian($english))->toBe($persian);
        }
    });

    test('JalaliDateService converts Gregorian to Jalali correctly', function () {
        $jalaliService = app(JalaliDateService::class);
        
        $date = Carbon::create(2024, 1, 1);
        $jalali = $jalaliService->toJalali($date);
        
        expect($jalali)->toMatch('/\d{4}\/\d{2}\/\d{2}/');
    });

    test('JalaliDateService converts Jalali to Gregorian correctly', function () {
        $jalaliService = app(JalaliDateService::class);
        
        $gregorian = $jalaliService->toGregorian('1403/01/01');
        
        expect($gregorian)->toBeInstanceOf(Carbon::class);
        expect($gregorian->year)->toBe(2024);
        expect($gregorian->month)->toBe(3);
        expect($gregorian->day)->toBe(20);
    });

    test('JalaliDateService formats date with time', function () {
        $jalaliService = app(JalaliDateService::class);
        
        $date = Carbon::create(2024, 3, 20, 14, 30, 0);
        $jalaliDateTime = $jalaliService->toJalaliDateTime($date);
        
        expect($jalaliDateTime)->toContain('1403/01/01');
        expect($jalaliDateTime)->toContain('14:30:00');
    });

    test('JalaliDateService gets correct month name', function () {
        $jalaliService = app(JalaliDateService::class);
        
        expect($jalaliService->getMonthName(1))->toBe('فروردین');
        expect($jalaliService->getMonthName(6))->toBe('شهریور');
        expect($jalaliService->getMonthName(12))->toBe('اسفند');
    });

    test('JalaliDateService gets correct day name', function () {
        $jalaliService = app(JalaliDateService::class);
        
        // 2024-03-20 is Wednesday
        $date = Carbon::create(2024, 3, 20);
        expect($jalaliService->getDayName($date))->toBe('چهارشنبه');
    });

    test('JalaliDateService formats human-readable date', function () {
        $jalaliService = app(JalaliDateService::class);
        
        $date = Carbon::create(2024, 3, 20);
        $humanReadable = $jalaliService->toHumanReadable($date);
        
        expect($humanReadable)->toContain('چهارشنبه');
        expect($humanReadable)->toContain('فروردین');
        expect($humanReadable)->toContain('1403');
    });

    test('PersianNumberService converts English to Persian digits', function () {
        $persianService = app(PersianNumberService::class);
        
        expect($persianService->toPersian('123'))->toBe('۱۲۳');
        expect($persianService->toPersian('0'))->toBe('۰');
        expect($persianService->toPersian('9876543210'))->toBe('۹۸۷۶۵۴۳۲۱۰');
    });

    test('PersianNumberService converts Persian to English digits', function () {
        $persianService = app(PersianNumberService::class);
        
        expect($persianService->toEnglish('۱۲۳'))->toBe('123');
        expect($persianService->toEnglish('۰'))->toBe('0');
        expect($persianService->toEnglish('۹۸۷۶۵۴۳۲۱۰'))->toBe('9876543210');
    });

    test('PersianNumberService formats currency correctly', function () {
        $persianService = app(PersianNumberService::class);
        
        $formatted = $persianService->formatCurrency(1000000);
        
        expect($formatted)->toContain('۱,۰۰۰,۰۰۰');
        expect($formatted)->toContain('ریال');
    });

    test('PersianNumberService formats currency with English digits', function () {
        $persianService = app(PersianNumberService::class);
        
        $formatted = $persianService->formatCurrency(1000000, false);
        
        expect($formatted)->toContain('1,000,000');
        expect($formatted)->toContain('ریال');
    });

    test('PersianNumberService formats number with thousand separators', function () {
        $persianService = app(PersianNumberService::class);
        
        $formatted = $persianService->formatNumber(1234567);
        
        expect($formatted)->toBe('۱,۲۳۴,۵۶۷');
    });

    test('PersianNumberService formats decimal numbers', function () {
        $persianService = app(PersianNumberService::class);
        
        $formatted = $persianService->formatDecimal(1234.56, 2);
        
        expect($formatted)->toContain('۱,۲۳۴');
        expect($formatted)->toContain('۵۶');
    });

    test('app locale is set to Farsi', function () {
        expect(config('app.locale'))->toBe('fa');
    });
});
