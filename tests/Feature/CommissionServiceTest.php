<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Listing;
use App\Models\SiteSetting;
use App\Services\CommissionService;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommissionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CommissionService $commissionService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->commissionService = app(CommissionService::class);
    }

    public function test_calculate_deposit_with_percentage()
    {
        SiteSetting::set('deposit_type', 'percentage');
        SiteSetting::set('deposit_percentage', 10);

        $deposit = $this->commissionService->calculateDeposit(1000000);

        expect($deposit)->toBe(100000);
    }

    public function test_calculate_deposit_with_fixed_amount()
    {
        SiteSetting::set('deposit_type', 'fixed');
        SiteSetting::set('deposit_fixed_amount', 500000);

        $deposit = $this->commissionService->calculateDeposit(1000000);

        expect($deposit)->toBe(500000);
    }

    public function test_calculate_commission_with_percentage()
    {
        SiteSetting::set('commission_type', 'percentage');
        SiteSetting::set('commission_percentage', 5);

        $commission = $this->commissionService->calculateCommission(1000000);

        expect($commission)->toBe(50000);
    }

    public function test_calculate_commission_with_fixed_amount()
    {
        SiteSetting::set('commission_type', 'fixed');
        SiteSetting::set('commission_fixed_amount', 100000);

        $commission = $this->commissionService->calculateCommission(1000000);

        expect($commission)->toBe(100000);
    }

    public function test_deduct_commission_from_buyer_only()
    {
        $buyer = User::factory()->create(['role' => 'buyer']);
        $seller = User::factory()->create(['role' => 'seller']);
        $siteUser = User::factory()->create(['id' => 1]);

        $buyer->wallet()->create(['balance' => 2000000, 'frozen' => 0]);
        $seller->wallet()->create(['balance' => 0, 'frozen' => 0]);
        $siteUser->wallet()->create(['balance' => 0, 'frozen' => 0]);

        $listing = Listing::factory()->create([
            'seller_id' => $seller->id,
            'starting_price' => 1000000,
        ]);

        SiteSetting::set('commission_type', 'percentage');
        SiteSetting::set('commission_percentage', 5);
        SiteSetting::set('commission_payer', 'buyer');

        $result = $this->commissionService->deductCommission($listing, $buyer, 1000000);

        expect($result['total_commission'])->toBe(50000);
        expect($result['buyer_commission'])->toBe(50000);
        expect($result['seller_commission'])->toBe(0);

        $buyer->wallet->refresh();
        $siteUser->wallet->refresh();

        expect($buyer->wallet->balance)->toBe(1950000);
        expect($siteUser->wallet->balance)->toBe(50000);
    }

    public function test_deduct_commission_from_seller_only()
    {
        $buyer = User::factory()->create(['role' => 'buyer']);
        $seller = User::factory()->create(['role' => 'seller']);
        $siteUser = User::factory()->create(['id' => 1]);

        $buyer->wallet()->create(['balance' => 2000000, 'frozen' => 0]);
        $seller->wallet()->create(['balance' => 1000000, 'frozen' => 0]);
        $siteUser->wallet()->create(['balance' => 0, 'frozen' => 0]);

        $listing = Listing::factory()->create([
            'seller_id' => $seller->id,
            'starting_price' => 1000000,
        ]);

        SiteSetting::set('commission_type', 'percentage');
        SiteSetting::set('commission_percentage', 5);
        SiteSetting::set('commission_payer', 'seller');

        $result = $this->commissionService->deductCommission($listing, $buyer, 1000000);

        expect($result['total_commission'])->toBe(50000);
        expect($result['buyer_commission'])->toBe(0);
        expect($result['seller_commission'])->toBe(50000);

        $seller->wallet->refresh();
        $siteUser->wallet->refresh();

        expect($seller->wallet->balance)->toBe(950000);
        expect($siteUser->wallet->balance)->toBe(50000);
    }

    public function test_deduct_commission_from_both()
    {
        $buyer = User::factory()->create(['role' => 'buyer']);
        $seller = User::factory()->create(['role' => 'seller']);
        $siteUser = User::factory()->create(['id' => 1]);

        $buyer->wallet()->create(['balance' => 2000000, 'frozen' => 0]);
        $seller->wallet()->create(['balance' => 1000000, 'frozen' => 0]);
        $siteUser->wallet()->create(['balance' => 0, 'frozen' => 0]);

        $listing = Listing::factory()->create([
            'seller_id' => $seller->id,
            'starting_price' => 1000000,
        ]);

        SiteSetting::set('commission_type', 'percentage');
        SiteSetting::set('commission_percentage', 5);
        SiteSetting::set('commission_payer', 'both');
        SiteSetting::set('commission_split_percentage', 60);

        $result = $this->commissionService->deductCommission($listing, $buyer, 1000000);

        expect($result['total_commission'])->toBe(50000);
        expect($result['buyer_commission'])->toBe(30000);
        expect($result['seller_commission'])->toBe(20000);

        $buyer->wallet->refresh();
        $seller->wallet->refresh();
        $siteUser->wallet->refresh();

        expect($buyer->wallet->balance)->toBe(1970000);
        expect($seller->wallet->balance)->toBe(980000);
        expect($siteUser->wallet->balance)->toBe(50000);
    }
}
