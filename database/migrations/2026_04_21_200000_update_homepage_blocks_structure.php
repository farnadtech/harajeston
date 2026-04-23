<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ساختار جدید: آرایه‌ای از blocks که هر کدوم type و config دارن
        $defaultBlocks = [
            [
                'id'     => 'block_1',
                'type'   => 'hero',
                'config' => [
                    'title'             => 'بهترین مزایده‌های آنلاین',
                    'subtitle'          => 'هزاران کالای منحصربه‌فرد در انتظار شماست',
                    'button_text'       => 'مشاهده مزایده‌ها',
                    'bg_color'          => '#1e40af',
                    'use_listing_image' => true,
                    'show_side_banners' => true,
                    'custom_image'      => null,
                ],
            ],
            [
                'id'     => 'block_2',
                'type'   => 'categories',
                'config' => [
                    'title'  => 'دسته‌بندی‌های محبوب',
                    'count'  => 8,
                    'style'  => 'circle',
                ],
            ],
            [
                'id'     => 'block_3',
                'type'   => 'listings_grid',
                'config' => [
                    'title'    => 'مزایده‌های داغ',
                    'subtitle' => 'فرصت‌های استثنایی با زمان محدود',
                    'icon'     => 'local_fire_department',
                    'icon_bg'  => 'bg-red-100',
                    'icon_color' => 'text-red-600',
                    'count'    => 8,
                    'columns'  => 4,
                    'filter'   => 'active',
                    'sort'     => 'ending_soon',
                ],
            ],
            [
                'id'     => 'block_4',
                'type'   => 'trust_badges',
                'config' => [
                    'badges' => [
                        ['icon' => 'verified_user', 'title' => 'ضمانت اصالت کالا',      'desc' => 'تایید کارشناسی تمامی کالاها قبل از مزایده'],
                        ['icon' => 'local_shipping', 'title' => 'ارسال سریع و بیمه شده', 'desc' => 'ارسال به سراسر کشور با بسته بندی ایمن'],
                        ['icon' => 'support_agent',  'title' => 'پشتیبانی ۲۴ ساعته',    'desc' => 'پاسخگویی به سوالات شما در تمام مراحل'],
                    ],
                ],
            ],
        ];

        DB::table('homepage_settings')->updateOrInsert(
            ['key' => 'blocks'],
            ['value' => json_encode($defaultBlocks, JSON_UNESCAPED_UNICODE), 'updated_at' => now(), 'created_at' => now()]
        );

        // card style جداگانه نگه می‌داریم
        DB::table('homepage_settings')->updateOrInsert(
            ['key' => 'card_style'],
            ['value' => 'classic', 'updated_at' => now(), 'created_at' => now()]
        );
    }

    public function down(): void
    {
        DB::table('homepage_settings')->where('key', 'blocks')->delete();
    }
};
