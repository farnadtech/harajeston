<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homepage_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->longText('value')->nullable();
            $table->timestamps();
        });

        // مقادیر پیش‌فرض
        $defaults = [
            // چیدمان بخش‌ها
            'sections_order' => json_encode([
                ['id' => 'hero', 'label' => 'بنر اصلی', 'enabled' => true, 'order' => 1],
                ['id' => 'categories', 'label' => 'دسته‌بندی‌ها', 'enabled' => true, 'order' => 2],
                ['id' => 'hot_auctions', 'label' => 'مزایده‌های داغ', 'enabled' => true, 'order' => 3],
                ['id' => 'featured_banner', 'label' => 'بنر ویژه', 'enabled' => false, 'order' => 4],
                ['id' => 'latest_listings', 'label' => 'جدیدترین آگهی‌ها', 'enabled' => false, 'order' => 5],
                ['id' => 'trust_badges', 'label' => 'نشان‌های اعتماد', 'enabled' => true, 'order' => 6],
                ['id' => 'newsletter', 'label' => 'خبرنامه', 'enabled' => false, 'order' => 7],
                ['id' => 'stats', 'label' => 'آمار سایت', 'enabled' => false, 'order' => 8],
            ]),

            // کارت محصول
            'card_style' => 'classic', // classic, modern, minimal, horizontal

            // تنظیمات Hero
            'hero_enabled' => '1',
            'hero_title' => 'بهترین مزایده‌های آنلاین',
            'hero_subtitle' => 'هزاران کالای منحصربه‌فرد در انتظار شماست',
            'hero_button_text' => 'مشاهده مزایده‌ها',
            'hero_bg_color' => '#1e40af',
            'hero_use_listing_image' => '1',
            'hero_side_banners' => '1',

            // تنظیمات دسته‌بندی
            'categories_title' => 'دسته‌بندی‌های محبوب',
            'categories_count' => '8',
            'categories_style' => 'circle', // circle, card, pill

            // تنظیمات مزایده‌های داغ
            'hot_auctions_title' => 'مزایده‌های داغ',
            'hot_auctions_subtitle' => 'فرصت‌های استثنایی با زمان محدود',
            'hot_auctions_count' => '8',
            'hot_auctions_columns' => '4', // 2, 3, 4

            // تنظیمات جدیدترین آگهی‌ها
            'latest_title' => 'جدیدترین آگهی‌ها',
            'latest_count' => '6',
            'latest_columns' => '3',

            // تنظیمات نشان‌های اعتماد
            'trust_badges' => json_encode([
                ['icon' => 'verified_user', 'title' => 'ضمانت اصالت کالا', 'desc' => 'تایید کارشناسی تمامی کالاها قبل از مزایده', 'enabled' => true],
                ['icon' => 'local_shipping', 'title' => 'ارسال سریع و بیمه شده', 'desc' => 'ارسال به سراسر کشور با بسته بندی ایمن', 'enabled' => true],
                ['icon' => 'support_agent', 'title' => 'پشتیبانی ۲۴ ساعته', 'desc' => 'پاسخگویی به سوالات شما در تمام مراحل', 'enabled' => true],
                ['icon' => 'security', 'title' => 'پرداخت امن', 'desc' => 'درگاه پرداخت معتبر و رمزگذاری شده', 'enabled' => false],
            ]),

            // تنظیمات آمار
            'stats_items' => json_encode([
                ['icon' => 'gavel', 'value' => '۱۰,۰۰۰+', 'label' => 'مزایده موفق', 'enabled' => true],
                ['icon' => 'people', 'value' => '۵۰,۰۰۰+', 'label' => 'کاربر فعال', 'enabled' => true],
                ['icon' => 'store', 'value' => '۲,۰۰۰+', 'label' => 'فروشنده معتبر', 'enabled' => true],
                ['icon' => 'star', 'value' => '۴.۸', 'label' => 'امتیاز رضایت', 'enabled' => true],
            ]),

            // تنظیمات خبرنامه
            'newsletter_title' => 'عضویت در خبرنامه',
            'newsletter_subtitle' => 'از جدیدترین مزایده‌ها باخبر شوید',
            'newsletter_bg_color' => '#1e40af',

            // بنر ویژه
            'featured_banner_title' => 'پیشنهاد ویژه',
            'featured_banner_subtitle' => 'تخفیف‌های استثنایی',
            'featured_banner_button' => 'مشاهده',
            'featured_banner_bg' => '#f59e0b',
        ];

        foreach ($defaults as $key => $value) {
            DB::table('homepage_settings')->insert([
                'key' => $key,
                'value' => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_settings');
    }
};
