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
            'require_approval' => SiteSetting::get('require_listing_approval', false),
            'default_show_before_start' => SiteSetting::get('default_show_before_start', false),
            'default_bid_increment' => SiteSetting::get('default_bid_increment', 10000),
        ];

        $otpEnabled = SiteSetting::get('otp_enabled', true);

        return view('admin.settings.index', compact('depositSettings', 'commissionSettings', 'sellerSettings', 'auctionDurationSettings', 'walletSettings', 'loserFeeSettings', 'forfeitSettings', 'auctionReleaseSettings', 'listingSettings', 'otpEnabled'));
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
     * به‌روزرسانی تنظیمات احراز هویت
     */
    public function updateVerification(Request $request)
    {
        $requireVerification = $request->has('require_user_verification');
        
        SiteSetting::set('require_user_verification', $requireVerification, 'boolean');

        return redirect()->route('admin.settings.index')
            ->with('success', 'تنظیمات احراز هویت با موفقیت به‌روزرسانی شد.');
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
        $validated = $request->validate([
            'default_bid_increment' => 'required|integer|min:1000',
        ]);

        $requireApproval = $request->has('require_listing_approval');
        $defaultShowBeforeStart = $request->has('default_show_before_start');
        
        // دریافت مقدار قبلی برای مقایسه
        $oldBidIncrement = SiteSetting::get('default_bid_increment', 10000);
        $newBidIncrement = $validated['default_bid_increment'];
        
        SiteSetting::set('require_listing_approval', $requireApproval, 'boolean');
        SiteSetting::set('default_show_before_start', $defaultShowBeforeStart, 'boolean');
        SiteSetting::set('default_bid_increment', $newBidIncrement, 'integer');

        // اگر گام افزایش تغییر کرده، همه آگهی‌ها رو آپدیت کن
        if ($oldBidIncrement != $newBidIncrement) {
            $updatedCount = \App\Models\Listing::query()->update([
                'bid_increment' => $newBidIncrement
            ]);
            
            return redirect()->route('admin.settings.index')
                ->with('success', "تنظیمات آگهی‌ها با موفقیت به‌روزرسانی شد. گام افزایش برای {$updatedCount} آگهی اعمال شد.");
        }

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

    /**
     * فعال/غیرفعال کردن سیستم OTP
     */
    public function updateOtp(Request $request)
    {
        $enabled = $request->has('otp_enabled');
        SiteSetting::set('otp_enabled', $enabled, 'boolean');

        return redirect()->route('admin.settings.index')
            ->with('success', $enabled ? 'سیستم OTP فعال شد.' : 'سیستم OTP غیرفعال شد.');
    }

    /**
     * صفحه تنظیمات عمومی سایت
     */
    public function general()
    {
        $settings = [
            'site_name'           => SiteSetting::get('site_name', 'حراج‌استون'),
            'site_tagline'        => SiteSetting::get('site_tagline', ''),
            'site_description'    => SiteSetting::get('site_description', ''),
            'site_logo'           => SiteSetting::get('site_logo', ''),
            'site_favicon'        => SiteSetting::get('site_favicon', ''),
            'site_icon'           => SiteSetting::get('site_icon', 'gavel'),
            'site_email'          => SiteSetting::get('site_email', ''),
            'site_phone'          => SiteSetting::get('site_phone', ''),
            'site_address'        => SiteSetting::get('site_address', ''),
            'color_primary'       => SiteSetting::get('color_primary', '#135bec'),
            'color_primary_hover' => SiteSetting::get('color_primary_hover', '#0e4bc7'),
            'color_secondary'     => SiteSetting::get('color_secondary', '#f97316'),
            'color_bg'            => SiteSetting::get('color_bg', '#f1f3f7'),
            'color_text'          => SiteSetting::get('color_text', '#0d121b'),
            'footer_text'         => SiteSetting::get('footer_text', ''),
            'social_instagram'    => SiteSetting::get('social_instagram', ''),
            'social_telegram'     => SiteSetting::get('social_telegram', ''),
            'social_whatsapp'     => SiteSetting::get('social_whatsapp', ''),
        ];

        return view('admin.settings.general', compact('settings'));
    }

    /**
     * ذخیره تنظیمات عمومی
     */
    public function updateGeneral(Request $request)
    {
        $request->validate([
            'site_name'           => 'required|string|max:100',
            'site_tagline'        => 'nullable|string|max:200',
            'site_description'    => 'nullable|string|max:500',
            'site_icon'           => 'nullable|string|max:50',
            'site_email'          => 'nullable|email|max:100',
            'site_phone'          => 'nullable|string|max:20',
            'site_address'        => 'nullable|string|max:300',
            'color_primary'       => 'required|regex:/^#[0-9a-fA-F]{6}$/',
            'color_primary_hover' => 'required|regex:/^#[0-9a-fA-F]{6}$/',
            'color_secondary'     => 'required|regex:/^#[0-9a-fA-F]{6}$/',
            'color_bg'            => 'required|regex:/^#[0-9a-fA-F]{6}$/',
            'color_text'          => 'required|regex:/^#[0-9a-fA-F]{6}$/',
            'footer_text'         => 'nullable|string|max:500',
            'social_instagram'    => 'nullable|url|max:200',
            'social_telegram'     => 'nullable|string|max:200',
            'social_whatsapp'     => 'nullable|string|max:20',
        ]);

        $fields = [
            'site_name', 'site_tagline', 'site_description', 'site_icon',
            'site_email', 'site_phone', 'site_address',
            'color_primary', 'color_primary_hover', 'color_secondary', 'color_bg', 'color_text',
            'footer_text', 'social_instagram', 'social_telegram', 'social_whatsapp',
        ];

        foreach ($fields as $field) {
            SiteSetting::set($field, $request->input($field, ''));
        }

        // Clear all settings cache
        SiteSetting::clearCache();

        return redirect()->route('admin.settings.general')
            ->with('success', 'تنظیمات عمومی با موفقیت ذخیره شد.');
    }

    /**
     * آپلود لوگو یا فاویکون
     */
    public function uploadLogo(Request $request)
    {
        try {
            $request->validate([
                'file'  => 'required|file|mimes:png,jpg,jpeg,gif,webp,svg|max:2048',
                'type'  => 'required|in:site_logo,site_favicon',
            ]);

            $path = $request->file('file')->store('site', 'public');
            SiteSetting::set($request->type, $path);
            SiteSetting::clearCache();

            // Build URL using APP_URL directly to avoid XAMPP subfolder issues
            $url = rtrim(config('app.url'), '/') . '/storage/' . $path;

            return response()->json([
                'success' => true,
                'path' => $path,
                'url' => $url,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => implode(' ', $e->validator->errors()->all())], 422);
        } catch (\Exception $e) {
            \Log::error('Logo upload error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
