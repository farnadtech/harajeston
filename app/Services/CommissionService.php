<?php

namespace App\Services;

use App\Models\SiteSetting;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CommissionService
{
    public function __construct(
        protected WalletService $walletService
    ) {}

    /**
     * محاسبه مبلغ سپرده بر اساس تنظیمات سایت
     */
    public function calculateDeposit(int $basePrice): int
    {
        $depositType = SiteSetting::get('deposit_type', 'percentage');
        
        if ($depositType === 'fixed') {
            return (int) SiteSetting::get('deposit_fixed_amount', 1000000);
        }
        
        // percentage
        $percentage = (float) SiteSetting::get('deposit_percentage', 10);
        return (int) ($basePrice * ($percentage / 100));
    }

    /**
     * محاسبه مبلغ کمیسیون بر اساس تنظیمات سایت یا دسته‌بندی
     */
    public function calculateCommission(int $finalPrice, ?int $categoryId = null): int
    {
        $commissionType = SiteSetting::get('commission_type', 'percentage');
        
        // اگر نوع محاسبه بر اساس دسته‌بندی باشد
        if ($commissionType === 'category' && $categoryId) {
            return $this->calculateCategoryCommission($finalPrice, $categoryId);
        }
        
        if ($commissionType === 'fixed') {
            return (int) SiteSetting::get('commission_fixed_amount', 50000);
        }
        
        // percentage
        $percentage = (float) SiteSetting::get('commission_percentage', 5);
        return (int) ($finalPrice * ($percentage / 100));
    }

    /**
     * محاسبه کمیسیون بر اساس دسته‌بندی
     */
    protected function calculateCategoryCommission(int $finalPrice, int $categoryId): int
    {
        $category = \App\Models\Category::find($categoryId);
        
        if (!$category) {
            // اگر دسته‌بندی پیدا نشد، از تنظیمات پیش‌فرض استفاده کن
            $percentage = (float) SiteSetting::get('commission_percentage', 5);
            return (int) ($finalPrice * ($percentage / 100));
        }
        
        // جستجوی کمیسیون برای این دسته‌بندی
        $commission = \App\Models\CategoryCommission::where('category_id', $categoryId)->first();
        
        // اگر کمیسیون برای این دسته تعریف نشده، از دسته والد استفاده کن
        if (!$commission && $category->parent_id) {
            return $this->calculateCategoryCommission($finalPrice, $category->parent_id);
        }
        
        // اگر هیچ کمیسیونی پیدا نشد، از تنظیمات پیش‌فرض استفاده کن
        if (!$commission) {
            $percentage = (float) SiteSetting::get('commission_percentage', 5);
            return (int) ($finalPrice * ($percentage / 100));
        }
        
        // محاسبه بر اساس نوع کمیسیون
        if ($commission->type === 'fixed') {
            return (int) $commission->fixed_amount;
        }
        
        return (int) ($finalPrice * ($commission->percentage / 100));
    }

    /**
     * کسر کمیسیون از طرفین و واریز به کیف پول سایت
     */
    public function deductCommission(Listing $listing, User $buyer, int $finalPrice): array
    {
        $commission = $this->calculateCommission($finalPrice, $listing->category_id);
        $payer = SiteSetting::get('commission_payer', 'buyer');
        
        $buyerCommission = 0;
        $sellerCommission = 0;

        DB::transaction(function () use ($listing, $buyer, $commission, $payer, &$buyerCommission, &$sellerCommission) {
            $seller = $listing->seller;

            if ($payer === 'buyer') {
                // کمیسیون فقط از خریدار
                $buyerCommission = $commission;
                $this->walletService->deduct(
                    $buyer,
                    $buyerCommission,
                    sprintf('کمیسیون سایت - خرید: %s', $listing->title),
                    $listing
                );
            } elseif ($payer === 'seller') {
                // کمیسیون فقط از فروشنده
                $sellerCommission = $commission;
                $this->walletService->deduct(
                    $seller,
                    $sellerCommission,
                    sprintf('کمیسیون سایت - فروش: %s', $listing->title),
                    $listing
                );
            } else {
                // both - تقسیم بین خریدار و فروشنده
                $splitPercentage = (float) SiteSetting::get('commission_split_percentage', 50);
                $buyerCommission = (int) ($commission * ($splitPercentage / 100));
                $sellerCommission = $commission - $buyerCommission;

                $this->walletService->deduct(
                    $buyer,
                    $buyerCommission,
                    sprintf('کمیسیون سایت (سهم خریدار) - خرید: %s', $listing->title),
                    $listing
                );

                $this->walletService->deduct(
                    $seller,
                    $sellerCommission,
                    sprintf('کمیسیون سایت (سهم فروشنده) - فروش: %s', $listing->title),
                    $listing
                );
            }

            // واریز کل کمیسیون به کیف پول سایت (user_id = 1 یا یک حساب مخصوص)
            $this->depositToSiteWallet(
                $commission,
                sprintf('کمیسیون از معامله: %s', $listing->title),
                $listing
            );
        });

        return [
            'total_commission' => $commission,
            'buyer_commission' => $buyerCommission,
            'seller_commission' => $sellerCommission,
        ];
    }

    /**
     * واریز به کیف پول سایت
     */
    protected function depositToSiteWallet(int $amount, string $description, Listing $listing): void
    {
        // فرض می‌کنیم user_id = 1 حساب سایت است
        // یا می‌توانید یک جدول جداگانه برای کیف پول سایت داشته باشید
        $siteUser = User::find(1);
        
        if ($siteUser) {
            $this->walletService->addFunds(
                $siteUser,
                $amount,
                $description
            );
        }
    }

    /**
     * دریافت تنظیمات سپرده
     */
    public function getDepositSettings(): array
    {
        return [
            'type' => SiteSetting::get('deposit_type', 'percentage'),
            'fixed_amount' => (int) SiteSetting::get('deposit_fixed_amount', 1000000),
            'percentage' => (float) SiteSetting::get('deposit_percentage', 10),
        ];
    }

    /**
     * دریافت تنظیمات کمیسیون
     */
    public function getCommissionSettings(): array
    {
        return [
            'type' => SiteSetting::get('commission_type', 'percentage'),
            'fixed_amount' => (int) SiteSetting::get('commission_fixed_amount', 50000),
            'percentage' => (float) SiteSetting::get('commission_percentage', 5),
            'payer' => SiteSetting::get('commission_payer', 'buyer'),
            'split_percentage' => (float) SiteSetting::get('commission_split_percentage', 50),
        ];
    }
}
