<?php

namespace App\Services;

use App\Models\WalletTransaction;
use App\Models\Order;
use App\Models\Listing;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinancialReportService
{
    /**
     * دریافت خلاصه درآمد سایت
     */
    public function getSiteRevenueSummary(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate = $startDate ?? Carbon::now()->startOfMonth();
        $endDate = $endDate ?? Carbon::now()->endOfMonth();

        // کمیسیون‌های دریافتی (تراکنش‌های واریز به کیف پول سایت)
        $siteWallet = User::find(1)?->wallet;
        
        if (!$siteWallet) {
            return $this->getEmptyRevenueSummary();
        }

        // کمیسیون‌های دریافتی در بازه زمانی
        $commissions = WalletTransaction::where('wallet_id', $siteWallet->id)
            ->where('type', 'credit')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('description', 'LIKE', '%کمیسیون%')
            ->sum('amount');

        // سپرده‌های ضبط شده
        $forfeitedDeposits = WalletTransaction::where('wallet_id', $siteWallet->id)
            ->where('type', 'credit')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('description', 'LIKE', '%ضبط سپرده%')
            ->sum('amount');

        // کل درآمد
        $totalRevenue = $commissions + $forfeitedDeposits;

        // تعداد معاملات موفق
        $successfulAuctions = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'delivered')
            ->count();

        // حجم معاملات (مبلغ کل فروش)
        $totalSalesVolume = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'delivered')
            ->sum('total');

        return [
            'total_revenue' => $totalRevenue,
            'commissions' => $commissions,
            'forfeited_deposits' => $forfeitedDeposits,
            'successful_auctions' => $successfulAuctions,
            'total_sales_volume' => $totalSalesVolume,
            'average_commission_per_sale' => $successfulAuctions > 0 ? $commissions / $successfulAuctions : 0,
            'commission_rate' => $totalSalesVolume > 0 ? ($commissions / $totalSalesVolume) * 100 : 0,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];
    }

    /**
     * دریافت گزارش روزانه درآمد
     */
    public function getDailyRevenue(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate = $startDate ?? Carbon::now()->subDays(30);
        $endDate = $endDate ?? Carbon::now();

        $siteWallet = User::find(1)?->wallet;
        
        if (!$siteWallet) {
            return [];
        }

        $dailyData = WalletTransaction::where('wallet_id', $siteWallet->id)
            ->where('type', 'credit')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(CASE WHEN description LIKE "%کمیسیون%" THEN amount ELSE 0 END) as commissions'),
                DB::raw('SUM(CASE WHEN description LIKE "%ضبط سپرده%" THEN amount ELSE 0 END) as forfeited_deposits'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return $dailyData->toArray();
    }

    /**
     * دریافت گزارش ماهانه درآمد
     */
    public function getMonthlyRevenue(int $year = null): array
    {
        $year = $year ?? Carbon::now()->year;
        $siteWallet = User::find(1)?->wallet;
        
        if (!$siteWallet) {
            return [];
        }

        $monthlyData = WalletTransaction::where('wallet_id', $siteWallet->id)
            ->where('type', 'credit')
            ->whereYear('created_at', $year)
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(CASE WHEN description LIKE "%کمیسیون%" THEN amount ELSE 0 END) as commissions'),
                DB::raw('SUM(CASE WHEN description LIKE "%ضبط سپرده%" THEN amount ELSE 0 END) as forfeited_deposits'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        return $monthlyData->toArray();
    }

    /**
     * دریافت جزئیات کمیسیون‌ها
     */
    public function getCommissionDetails(?Carbon $startDate = null, ?Carbon $endDate = null, int $perPage = 20)
    {
        $startDate = $startDate ?? Carbon::now()->startOfMonth();
        $endDate = $endDate ?? Carbon::now()->endOfMonth();

        $siteWallet = User::find(1)?->wallet;
        
        if (!$siteWallet) {
            return collect([]);
        }

        return WalletTransaction::where('wallet_id', $siteWallet->id)
            ->where('type', 'credit')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('description', 'LIKE', '%کمیسیون%')
            ->with(['wallet.user'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * دریافت آمار فروشندگان برتر
     */
    public function getTopSellers(?Carbon $startDate = null, ?Carbon $endDate = null, int $limit = 10): array
    {
        $startDate = $startDate ?? Carbon::now()->startOfMonth();
        $endDate = $endDate ?? Carbon::now()->endOfMonth();

        $topSellers = Order::whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', 'delivered')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('listings', 'order_items.listing_id', '=', 'listings.id')
            ->join('users', 'listings.seller_id', '=', 'users.id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                DB::raw('COUNT(DISTINCT orders.id) as total_sales'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderBy('total_revenue', 'desc')
            ->limit($limit)
            ->get();

        return $topSellers->toArray();
    }

    /**
     * دریافت آمار خریداران برتر
     */
    public function getTopBuyers(?Carbon $startDate = null, ?Carbon $endDate = null, int $limit = 10): array
    {
        $startDate = $startDate ?? Carbon::now()->startOfMonth();
        $endDate = $endDate ?? Carbon::now()->endOfMonth();

        $topBuyers = Order::whereBetween('orders.created_at', [$startDate, $endDate])
            ->where('orders.status', 'delivered')
            ->join('users', 'orders.buyer_id', '=', 'users.id')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                DB::raw('COUNT(orders.id) as total_purchases'),
                DB::raw('SUM(orders.total) as total_spent')
            )
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderBy('total_spent', 'desc')
            ->limit($limit)
            ->get();

        return $topBuyers->toArray();
    }

    /**
     * دریافت آمار دسته‌بندی‌ها
     */
    public function getCategoryStats(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate = $startDate ?? Carbon::now()->startOfMonth();
        $endDate = $endDate ?? Carbon::now()->endOfMonth();

        $categoryStats = Listing::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn('status', ['delivered', 'ended'])
            ->join('categories', 'listings.category_id', '=', 'categories.id')
            ->select(
                'categories.name as category_name',
                'categories.id as category_id',
                DB::raw('COUNT(*) as total_listings'),
                DB::raw('SUM(listings.current_price) as total_value'),
                DB::raw('AVG(listings.current_price) as average_price')
            )
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_value', 'desc')
            ->get();

        return $categoryStats->toArray();
    }

    /**
     * دریافت موجودی کیف پول سایت
     */
    public function getSiteWalletBalance(): array
    {
        $siteWallet = User::find(1)?->wallet;
        
        if (!$siteWallet) {
            return [
                'balance' => 0,
                'frozen' => 0,
                'available' => 0,
            ];
        }

        return [
            'balance' => $siteWallet->balance,
            'frozen' => $siteWallet->frozen,
            'available' => $siteWallet->balance - $siteWallet->frozen,
        ];
    }

    /**
     * دریافت آمار کلی پلتفرم
     */
    public function getPlatformStats(): array
    {
        return [
            'total_users' => User::count(),
            'total_sellers' => User::where('role', 'seller')->count(),
            'total_buyers' => User::where('role', 'buyer')->count(),
            'total_listings' => Listing::count(),
            'active_listings' => Listing::where('status', 'active')->count(),
            'completed_listings' => Listing::whereIn('status', ['ended', 'completed'])->count(),
            'total_orders' => Order::count(),
            'completed_orders' => Order::where('status', 'delivered')->count(),
            'total_bids' => DB::table('bids')->count(),
        ];
    }

    /**
     * خلاصه خالی برای زمانی که کیف پول سایت وجود ندارد
     */
    protected function getEmptyRevenueSummary(): array
    {
        return [
            'total_revenue' => 0,
            'commissions' => 0,
            'forfeited_deposits' => 0,
            'successful_auctions' => 0,
            'total_sales_volume' => 0,
            'average_commission_per_sale' => 0,
            'commission_rate' => 0,
            'start_date' => Carbon::now()->startOfMonth(),
            'end_date' => Carbon::now()->endOfMonth(),
        ];
    }

    /**
     * Export گزارش به CSV
     */
    public function exportToCSV(?Carbon $startDate = null, ?Carbon $endDate = null): string
    {
        $summary = $this->getSiteRevenueSummary($startDate, $endDate);
        $dailyRevenue = $this->getDailyRevenue($startDate, $endDate);

        $csv = "تاریخ,کمیسیون,سپرده ضبط شده,کل درآمد\n";
        
        foreach ($dailyRevenue as $day) {
            $csv .= sprintf(
                "%s,%d,%d,%d\n",
                $day['date'],
                $day['commissions'],
                $day['forfeited_deposits'],
                $day['total']
            );
        }

        return $csv;
    }
}
