<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Jalali date Blade directive
        \Illuminate\Support\Facades\Blade::directive('jalali', function ($expression) {
            return "<?php echo app(\App\Services\JalaliDateService::class)->toJalali($expression); ?>";
        });

        // Register Persian number Blade directive
        \Illuminate\Support\Facades\Blade::directive('persian', function ($expression) {
            return "<?php echo app(\App\Services\PersianNumberService::class)->toPersian($expression); ?>";
        });

        // Register currency formatting Blade directive
        \Illuminate\Support\Facades\Blade::directive('currency', function ($expression) {
            return "<?php echo app(\App\Services\PersianNumberService::class)->formatCurrency($expression); ?>";
        });

        // Register price formatting with Persian digits Blade directive
        \Illuminate\Support\Facades\Blade::directive('price', function ($expression) {
            return "<?php echo app(\App\Services\PersianNumberService::class)->formatNumber($expression, true); ?>";
        });

        // Register category components
        \Illuminate\Support\Facades\Blade::component('category-megamenu', \App\View\Components\CategoryMegamenu::class);
        \Illuminate\Support\Facades\Blade::component('category-selector', \App\View\Components\CategorySelector::class);
        \Illuminate\Support\Facades\Blade::component('listing-attributes', \App\View\Components\ListingAttributes::class);
    }
}
