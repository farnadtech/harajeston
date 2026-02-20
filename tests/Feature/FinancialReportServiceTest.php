<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Listing;
use App\Models\Order;
use App\Models\WalletTransaction;
use App\Services\FinancialReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class FinancialReportServiceTest extends TestCase
{
    use RefreshDatabase;

    protected FinancialReportService $service;
    protected User $siteUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(FinancialReportService::class);
        
        // ایجاد کاربر سایت (user_id = 1)
        $this->siteUser = User::factory()->create([
            'id' => 1,
            'role' => 'admin',
        ]);
        $this->siteUser->wallet()->create(['balance' => 0, 'frozen' => 0]);
    }

    public function test_get_site_revenue_summary()
    {
        // ایجاد تراکنش‌های کمیسیون
        WalletTransaction::create([
            'wallet_id' => $this->siteUser->wallet->id,
            'type' => 'credit',
            'amount' => 50000,
            'balance_before' => 0,
            'balance_after' => 50000,
            'frozen_before' => 0,
            'frozen_after' => 0,
            'description' => 'کمیسیون سایت - خرید: تست',
        ]);

        WalletTransaction::create([
            'wallet_id' => $this->siteUser->wallet->id,
            'type' => 'credit',
            'amount' => 100000,
            'balance_before' => 50000,
            'balance_after' => 150000,
            'frozen_before' => 0,
            'frozen_after' => 0,
            'description' => 'دریافت سپرده ضبط شده: تست',
        ]);

        $summary = $this->service->getSiteRevenueSummary();

        expect($summary['commissions'])->toBe(50000);
        expect($summary['forfeited_deposits'])->toBe(100000);
        expect($summary['total_revenue'])->toBe(150000);
    }

    public function test_get_daily_revenue()
    {
        $today = Carbon::now();
        
        WalletTransaction::create([
            'wallet_id' => $this->siteUser->wallet->id,
            'type' => 'credit',
            'amount' => 50000,
            'balance_before' => 0,
            'balance_after' => 50000,
            'frozen_before' => 0,
            'frozen_after' => 0,
            'description' => 'کمیسیون سایت',
            'created_at' => $today,
        ]);

        $dailyRevenue = $this->service->getDailyRevenue($today->copy()->startOfDay(), $today->copy()->endOfDay());

        expect($dailyRevenue)->toHaveCount(1);
        expect($dailyRevenue[0]['commissions'])->toBe(50000);
    }

    public function test_get_site_wallet_balance()
    {
        $this->siteUser->wallet->update([
            'balance' => 1000000,
            'frozen' => 200000,
        ]);

        $balance = $this->service->getSiteWalletBalance();

        expect($balance['balance'])->toBe(1000000);
        expect($balance['frozen'])->toBe(200000);
        expect($balance['available'])->toBe(800000);
    }

    public function test_get_platform_stats()
    {
        User::factory()->count(5)->create(['role' => 'buyer']);
        User::factory()->count(3)->create(['role' => 'seller']);
        Listing::factory()->count(10)->create();

        $stats = $this->service->getPlatformStats();

        expect($stats['total_users'])->toBeGreaterThanOrEqual(9); // 5 buyers + 3 sellers + 1 site user
        expect($stats['total_buyers'])->toBe(5);
        expect($stats['total_sellers'])->toBe(3);
        expect($stats['total_listings'])->toBe(10);
    }

    public function test_get_top_sellers()
    {
        $seller1 = User::factory()->create(['role' => 'seller']);
        $seller2 = User::factory()->create(['role' => 'seller']);
        
        $listing1 = Listing::factory()->create(['seller_id' => $seller1->id, 'current_price' => 1000000]);
        $listing2 = Listing::factory()->create(['seller_id' => $seller2->id, 'current_price' => 500000]);

        $buyer = User::factory()->create(['role' => 'buyer']);
        
        Order::factory()->create([
            'user_id' => $buyer->id,
            'total_amount' => 1000000,
            'status' => 'completed',
        ]);

        $topSellers = $this->service->getTopSellers();

        expect($topSellers)->toBeArray();
    }

    public function test_export_to_csv()
    {
        WalletTransaction::create([
            'wallet_id' => $this->siteUser->wallet->id,
            'type' => 'credit',
            'amount' => 50000,
            'balance_before' => 0,
            'balance_after' => 50000,
            'frozen_before' => 0,
            'frozen_after' => 0,
            'description' => 'کمیسیون سایت',
        ]);

        $csv = $this->service->exportToCSV();

        expect($csv)->toContain('تاریخ,کمیسیون,سپرده ضبط شده,کل درآمد');
        expect($csv)->toBeString();
    }
}
