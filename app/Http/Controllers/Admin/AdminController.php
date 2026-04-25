<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $now = now();
        $monthAgo = $now->copy()->subDays(30);
        $twoMonthsAgo = $now->copy()->subDays(60);
        $weekAgo = $now->copy()->subDays(7);
        $twoWeeksAgo = $now->copy()->subDays(14);

        // درآمد سایت = واریزی‌های کمیسیون به کیف پول سایت (user_id = 1)
        $siteUserId = 1;
        $siteRevenue = \App\Models\WalletTransaction::where('user_id', $siteUserId)
            ->where('type', 'deposit')
            ->where('status', 'completed')
            ->sum('amount');
        $siteRevenueLastMonth = \App\Models\WalletTransaction::where('user_id', $siteUserId)
            ->where('type', 'deposit')
            ->where('status', 'completed')
            ->where('created_at', '>=', $monthAgo)
            ->sum('amount');
        $siteRevenuePrevMonth = \App\Models\WalletTransaction::where('user_id', $siteUserId)
            ->where('type', 'deposit')
            ->where('status', 'completed')
            ->whereBetween('created_at', [$twoMonthsAgo, $monthAgo])
            ->sum('amount');
        $revenueGrowth = $siteRevenuePrevMonth > 0
            ? round((($siteRevenueLastMonth - $siteRevenuePrevMonth) / $siteRevenuePrevMonth) * 100)
            : ($siteRevenueLastMonth > 0 ? 100 : 0);

        // Active auctions
        $activeAuctions = Listing::where('status', 'active')->count();
        $activeAuctionsLastWeek = Listing::where('status', 'active')
            ->where('created_at', '>=', $weekAgo)->count();
        $activeAuctionsPrevWeek = Listing::where('status', 'active')
            ->whereBetween('created_at', [$twoWeeksAgo, $weekAgo])->count();
        $auctionsGrowth = $activeAuctionsPrevWeek > 0
            ? round((($activeAuctionsLastWeek - $activeAuctionsPrevWeek) / $activeAuctionsPrevWeek) * 100)
            : ($activeAuctionsLastWeek > 0 ? 100 : 0);

        // تعداد کل کاربران (غیر ادمین) با رشد ماهانه
        $totalUsers = User::where('role', '!=', 'admin')->count();
        $usersThisMonth = User::where('role', '!=', 'admin')
            ->where('created_at', '>=', $monthAgo)->count();
        $usersPrevMonth = User::where('role', '!=', 'admin')
            ->whereBetween('created_at', [$twoMonthsAgo, $monthAgo])->count();
        $usersGrowth = $usersPrevMonth > 0
            ? round((($usersThisMonth - $usersPrevMonth) / $usersPrevMonth) * 100)
            : ($usersThisMonth > 0 ? 100 : 0);

        // Pending approvals
        $pendingListings = Listing::where('status', 'pending')->count();
        $pendingSellerCount = User::where('seller_status', 'pending')->count();

        $stats = [
            'total_sales'       => $siteRevenueLastMonth,
            'prev_month_sales'  => $siteRevenuePrevMonth,
            'sales_growth'      => $revenueGrowth,
            'active_auctions'   => $activeAuctions,
            'auctions_growth'   => $auctionsGrowth,
            'active_users'      => $totalUsers,
            'users_growth'      => $usersGrowth,
            'pending_approvals' => $pendingListings + $pendingSellerCount,
            'pending_listings'  => $pendingListings,
            'pending_sellers'   => $pendingSellerCount,
        ];

        // Chart: active auctions per day for last 7 days
        $chartData = [];
        $chartLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = $now->copy()->subDays($i);
            $chartLabels[] = $day->locale('fa')->dayName;
            // Count auctions that were active on this day
            $chartData[] = Listing::where('status', 'active')
                ->whereDate('created_at', '<=', $day->toDateString())
                ->whereDate('ends_at', '>=', $day->toDateString())
                ->count();
        }

        // Pending sellers
        $pendingSellers = User::where('seller_status', 'pending')
            ->with('store')
            ->orderBy('seller_requested_at', 'desc')
            ->take(4)
            ->get();

        // Recent listings
        $recentListings = Listing::with(['seller.store', 'images'])
            ->latest()
            ->paginate(10);

        return view('admin.dashboard', compact(
            'stats', 'pendingSellers', 'recentListings', 'chartData', 'chartLabels'
        ));
    }
}
