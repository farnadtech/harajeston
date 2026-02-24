<?php

namespace Database\Seeders;

use App\Models\PaymentGateway;
use Illuminate\Database\Seeder;

class PaymentGatewaySeeder extends Seeder
{
    public function run(): void
    {
        $gateways = [
            [
                'name' => 'zarinpal',
                'display_name' => 'زرین‌پال',
                'is_active' => false,
                'credentials' => [
                    'merchant_id' => '',
                ],
                'sort_order' => 1,
            ],
            [
                'name' => 'zibal',
                'display_name' => 'زیبال',
                'is_active' => false,
                'credentials' => [
                    'merchant_id' => '',
                ],
                'sort_order' => 2,
            ],
            [
                'name' => 'vandar',
                'display_name' => 'وندار',
                'is_active' => false,
                'credentials' => [
                    'api_key' => '',
                ],
                'sort_order' => 3,
            ],
            [
                'name' => 'payping',
                'display_name' => 'پی‌پینگ',
                'is_active' => false,
                'credentials' => [
                    'api_key' => '',
                ],
                'sort_order' => 4,
            ],
        ];

        foreach ($gateways as $gateway) {
            PaymentGateway::updateOrCreate(
                ['name' => $gateway['name']],
                $gateway
            );
        }
    }
}
