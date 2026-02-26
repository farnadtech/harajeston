<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Services\CommissionService;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function __construct(
        protected CommissionService $commissionService
    ) {}

    /**
     * نمایش صفحه تنظیمات سایت
     */
    public function index()
    {
        $depositSettings = $this->commissionService->getDepositSettings();
        $commissionSettings = $this->commissionService->getCommissionSettings();
        $sellerSettings = [
            'require_approval' => SiteSetting::get('require_seller_approval', true)
        ];
        $auctionDurationSettings = [
            'force_duration' => SiteSetting::get('force_auction_duration', false),
            'duration_days' => SiteSetting::get('auction_duration_days', 7)
        ];
        $walletSettings = [
            'min_deposit' => SiteSetting::get('wallet_min_deposit', 10000),
            'max_deposit' => SiteSetting::get('wallet_max_deposit', 100000000),
            'min_withdraw' => SiteSetting::get('wallet_min_withdraw', 50000),
            'charge_tax' => SiteSetting::get('wallet_charge_tax', 0),
        ];
        $loserFeeSettings = [
            'enabled' => SiteSetting::get('loser_fee_enabled', false),
            'percentage' => SiteSetting::get('loser_fee_percentage', 5),
        ];
        $forfeitSettings = [
            'to_site_percentage' => SiteSetting::get('forfeit_to_site_percentage', 100),
        ];
        
        $auctionReleaseSettings = [
            'finalize_deadline_hours' => SiteSetting::get('auction_finalize_deadline_hours', 24),
        ];

        $listingSettings = [
            'require_approval' => SiteSetting::get('require_listing_approval', true),
            'default_show_before_start' => SiteSetting::get('default_show_before_start', false),
        ];

        return view('admin.settings.index', compact('depositSettings', 'commissionSettings', 'sellerSettings', 'auctionDurationSettings', 'walletSettings', 'loserFeeSettings', 'forfeitSettings', 'auctionReleaseSettings', 'listingSettings'));
    }

    /**
     * به‌روزرسانی تنظیمات سپرده
     */
    public function updateDeposit(Request $request)
    {
        $validated = $request->validate([
            'deposit_type' => 'required|in:fixed,percentage',
            'deposit_fixed_amount' => 'required|integer|min:0',
            'deposit_percentage' => 'required|numeric|min:0|max:100',
        ]);

        SiteSetting::set('deposit_type', $validated['deposit_type']);
        SiteSetting::set('deposit_fixed_amount', $validated['deposit_fixed_amount'], 'integer');
        SiteSetting::set('deposit_percentage', $validated['deposit_percentage'], 'decimal');

        return redirect()->route('admin.settings.index')
            ->with('success', 'تنظیمات سپرده با موفقیت به‌روزرسانی شد.');
    }

    /**
     * به‌روزرسانی تنظیمات کمیسیون
     */
    public function updateCommission(Request $request)
    {
        $validated = $request->validate([
            'commission_type' => 'required|in:fixed,percentage,category',
            'commission_fixed_amount' => 'required|integer|min:0',
            'commission_percentage' => 'required|numeric|min:0|max:100',
            'commission_payer' => 'required|in:buyer,seller,both',
            'commission_split_percentage' => 'required|numeric|min:0|max:100',
        ]);

        SiteSetting::set('commission_type', $validated['commission_type']);
        SiteSetting::set('commission_fixed_amount', $validated['commission_fixed_amount'], 'integer');
        SiteSetting::set('commission_percentage', $validated['commission_percentage'], 'decimal');
        SiteSetting::set('commission_payer', $validated['commission_payer']);
        SiteSetting::set('commission_split_percentage', $validated['commission_split_percentage'], 'decimal');

        return redirect()->route('admin.settings.index')
            ->with('success', 'تنظیمات کمیسیون با موفقیت به‌روزرسانی شد.');
    }

    /**
     * به‌روزرسانی تنظیمات فروشندگان
     */
    public function updateSeller(Request $request)
    {
        $requireApproval = $request->has('require_seller_approval');
        
        SiteSetting::set('require_seller_approval', $requireApproval, 'boolean');

        return redirect()->route('admin.settings.index')
            ->with('success', 'تنظیمات فروشندگان با موفقیت به‌روزرسانی شد.');
    }

    /**
     * به‌روزرسانی تنظیمات مدت زمان حراجی
     */
    public function updateAuctionDuration(Request $request)
    {
        $validated = $request->validate([
            'force_auction_duration' => 'nullable|boolean',
            'auction_duration_days' => 'required|integer|min:1|max:365',
        ]);

        $forceDuration = $request->has('force_auction_duration');
        
        SiteSetting::set('force_auction_duration', $forceDuration, 'boolean');
        SiteSetting::set('auction_duration_days', $validated['auction_duration_days'], 'integer');

        return redirect()->route('admin.settings.index')
            ->with('success', 'تنظیمات مدت زمان حراجی با موفقیت به‌روزرسانی شد.');
    }

    /**
     * به‌روزرسانی تنظیمات کیف پول
     */
    public function updateWallet(Request $request)
    {
        $validated = $request->validate([
            'wallet_min_deposit' => 'required|integer|min:1000',
            'wallet_max_deposit' => 'required|integer|min:10000',
            'wallet_min_withdraw' => 'required|integer|min:1000',
            'wallet_charge_tax' => 'required|numeric|min:0|max:100',
        ]);

        SiteSetting::set('wallet_min_deposit', $validated['wallet_min_deposit'], 'integer');
        SiteSetting::set('wallet_max_deposit', $validated['wallet_max_deposit'], 'integer');
        SiteSetting::set('wallet_min_withdraw', $validated['wallet_min_withdraw'], 'integer');
        SiteSetting::set('wallet_charge_tax', $validated['wallet_charge_tax'], 'decimal');

        return redirect()->route('admin.settings.index')
            ->with('success', 'تنظیمات کیف پول با موفقیت به‌روزرسانی شد.');
    }

    /**
     * به‌روزرسانی تنظیمات کارمزد بازندگان
     */
    public function updateLoserFee(Request $request)
    {
        $validated = $request->validate([
            'loser_fee_enabled' => 'nullable|boolean',
            'loser_fee_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $enabled = $request->has('loser_fee_enabled');
        
        SiteSetting::set('loser_fee_enabled', $enabled, 'boolean');
        SiteSetting::set('loser_fee_percentage', $validated['loser_fee_percentage'], 'decimal');

        return redirect()->route('admin.settings.index')
            ->with('success', 'تنظیمات کارمزد بازندگان با موفقیت به‌روزرسانی شد.');
    }

    /**
     * به‌روزرسانی تنظیمات سپرده ضبط شده
     */
    public function updateForfeit(Request $request)
    {
        $validated = $request->validate([
            'forfeit_to_site_percentage' => 'required|numeric|min:0|max:100',
        ]);

        SiteSetting::set('forfeit_to_site_percentage', $validated['forfeit_to_site_percentage'], 'decimal');

        return redirect()->route('admin.settings.index')
            ->with('success', 'تنظیمات سپرده ضبط شده با موفقیت به‌روزرسانی شد.');
    }

    /**
     * به‌روزرسانی تنظیمات آگهی‌ها
     */
    public function updateListing(Request $request)
    {
        $requireApproval = $request->has('require_listing_approval');
        $defaultShowBeforeStart = $request->has('default_show_before_start');
        
        SiteSetting::set('require_listing_approval', $requireApproval, 'boolean');
        SiteSetting::set('default_show_before_start', $defaultShowBeforeStart, 'boolean');

        return redirect()->route('admin.settings.index')
            ->with('success', 'تنظیمات آگهی‌ها با موفقیت به‌روزرسانی شد.');
    }

    /**
     * به‌روزرسانی تنظیمات آزادسازی پول حراجی
     */
    public function updateAuctionRelease(Request $request)
    {
        $request->validate([
            'auction_finalize_deadline_hours' => 'required|integer|min:1|max:168',
        ]);

        SiteSetting::set('auction_finalize_deadline_hours', $request->auction_finalize_deadline_hours);

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'تنظیمات زمان‌بندی حراجی با موفقیت به‌روزرسانی شد.');
    }

    /**
     * به‌روزرسانی تنظیمات جریمه لغو سفارش
     */
    public function updateCancellationPenalty(Request $request)
    {
        $validated = $request->validate([
            'order_cancellation_penalty_type' => 'required|in:percentage,fixed',
            'order_cancellation_penalty_value' => 'required|numeric|min:0',
        ]);

        SiteSetting::set('order_cancellation_penalty_type', $validated['order_cancellation_penalty_type']);
        SiteSetting::set('order_cancellation_penalty_value', $validated['order_cancellation_penalty_value'], 'decimal');

        return redirect()->route('admin.settings.index')
            ->with('success', 'تنظیمات جریمه لغو سفارش با موفقیت به‌روزرسانی شد.');
    }

    /**
     * به‌روزرسانی تنظیمات مهلت تست کالا
     */
    public function updateTestPeriod(Request $request)
    {
        $validated = $request->validate([
            'order_test_period_days' => 'required|integer|min:1|max:30',
        ]);

        SiteSetting::set('order_test_period_days', $validated['order_test_period_days'], 'integer');

        return redirect()->route('admin.settings.index')
            ->with('success', 'تنظیمات مهلت تست کالا با موفقیت به‌روزرسانی شد.');
    }
}
