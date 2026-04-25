<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * فقط داده‌های ضروری برای راه‌اندازی اولیه سایت
 * بدون هیچ داده demo یا تست
 */
class InstallSeeder extends Seeder
{
    public function run(): void
    {
        // تنظیمات پیش‌فرض سایت
        $settings = [
            ['key' => 'site_name',                    'value' => 'حراجینو',    'type' => 'string'],
            ['key' => 'require_seller_approval',      'value' => '1',          'type' => 'boolean'],
            ['key' => 'require_listing_approval',     'value' => '1',          'type' => 'boolean'],
            ['key' => 'deposit_type',                 'value' => 'percentage', 'type' => 'string'],
            ['key' => 'deposit_percentage',           'value' => '10',         'type' => 'decimal'],
            ['key' => 'deposit_fixed_amount',         'value' => '100000',     'type' => 'integer'],
            ['key' => 'commission_type',              'value' => 'percentage', 'type' => 'string'],
            ['key' => 'commission_percentage',        'value' => '5',          'type' => 'decimal'],
            ['key' => 'commission_fixed_amount',      'value' => '0',          'type' => 'integer'],
            ['key' => 'commission_payer',             'value' => 'seller',     'type' => 'string'],
            ['key' => 'commission_split_percentage',  'value' => '50',         'type' => 'decimal'],
            ['key' => 'wallet_min_deposit',           'value' => '10000',      'type' => 'integer'],
            ['key' => 'wallet_max_deposit',           'value' => '100000000',  'type' => 'integer'],
            ['key' => 'wallet_min_withdraw',          'value' => '50000',      'type' => 'integer'],
            ['key' => 'wallet_charge_tax',            'value' => '0',          'type' => 'decimal'],
            ['key' => 'auction_finalize_deadline_hours', 'value' => '24',      'type' => 'integer'],
            ['key' => 'default_bid_increment',        'value' => '10000',      'type' => 'integer'],
            ['key' => 'otp_enabled',                  'value' => '1',          'type' => 'boolean'],
            ['key' => 'require_user_verification',    'value' => '1',          'type' => 'boolean'],
            ['key' => 'loser_fee_enabled',            'value' => '0',          'type' => 'boolean'],
            ['key' => 'loser_fee_percentage',         'value' => '0',          'type' => 'decimal'],
            ['key' => 'forfeit_to_site_percentage',   'value' => '100',        'type' => 'decimal'],
            ['key' => 'order_cancellation_penalty_type',  'value' => 'percentage', 'type' => 'string'],
            ['key' => 'order_cancellation_penalty_value', 'value' => '10',     'type' => 'decimal'],
            ['key' => 'order_test_period_days',       'value' => '7',          'type' => 'integer'],
        ];

        foreach ($settings as $setting) {
            DB::table('site_settings')->updateOrInsert(
                ['key' => $setting['key']],
                ['value' => $setting['value'], 'type' => $setting['type']]
            );
        }

        // دسته‌بندی‌های پیش‌فرض
        $categories = [
            ['name' => 'الکترونیک',    'slug' => 'electronics',  'icon' => 'devices',       'parent_id' => null, 'sort_order' => 1],
            ['name' => 'خودرو',        'slug' => 'vehicles',     'icon' => 'directions_car', 'parent_id' => null, 'sort_order' => 2],
            ['name' => 'خانه و آشپزخانه', 'slug' => 'home',     'icon' => 'home',           'parent_id' => null, 'sort_order' => 3],
            ['name' => 'پوشاک',        'slug' => 'clothing',     'icon' => 'checkroom',      'parent_id' => null, 'sort_order' => 4],
            ['name' => 'کتاب و هنر',   'slug' => 'books-art',    'icon' => 'menu_book',      'parent_id' => null, 'sort_order' => 5],
            ['name' => 'ورزش',         'slug' => 'sports',       'icon' => 'sports_soccer',  'parent_id' => null, 'sort_order' => 6],
            ['name' => 'سایر',         'slug' => 'other',        'icon' => 'category',       'parent_id' => null, 'sort_order' => 7],
        ];

        foreach ($categories as $cat) {
            DB::table('categories')->updateOrInsert(
                ['slug' => $cat['slug']],
                array_merge($cat, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        // روش ارسال پیش‌فرض
        DB::table('shipping_methods')->updateOrInsert(
            ['name' => 'پست پیشتاز'],
            [
                'description' => 'ارسال از طریق پست پیشتاز',
                'base_price'  => 50000,
                'is_active'   => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]
        );
    }
}
