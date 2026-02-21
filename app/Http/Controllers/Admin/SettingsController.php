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

        return view('admin.settings.index', compact('depositSettings', 'commissionSettings', 'sellerSettings', 'auctionDurationSettings'));
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
            'commission_type' => 'required|in:fixed,percentage',
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
}
