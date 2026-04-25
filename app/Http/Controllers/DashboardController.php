<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Order;
use App\Models\Bid;

class DashboardController extends Controller
{
    /**
     * Main dashboard - redirects based on user role
     */
    public function index()
    {
        $user = auth()->user();
        
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->canSell()) {
            // If user is an active seller, show seller dashboard
            return $this->sellerDashboard();
        } else {
            // Otherwise show buyer dashboard (includes pending/rejected sellers)
            return $this->buyerDashboard();
        }
    }

    /**
     * Seller dashboard
     */
    public function sellerDashboard()
    {
        $user = auth()->user();

        // Base query
        $ordersQuery = Order::where('seller_id', $user->id);

        $stats = [
            'active_auctions'    => Listing::where('seller_id', $user->id)->where('status', 'active')->count(),
            'pending_listings'   => Listing::where('seller_id', $user->id)->where('status', 'pending')->count(),
            'scheduled_listings' => Listing::where('seller_id', $user->id)->where('status', 'scheduled')->count(),
            'completed_auctions' => Listing::where('seller_id', $user->id)->where('status', 'completed')->count(),
            'total_sales'        => (clone $ordersQuery)->where('status', 'completed')->sum('total'),
        ];

        // Financial breakdown by status
        $financials = [
            'completed'   => ['count' => 0, 'total' => 0, 'label' => 'تکمیل‌شده',    'color' => 'green'],
            'processing'  => ['count' => 0, 'total' => 0, 'label' => 'در پردازش',    'color' => 'blue'],
            'shipped'     => ['count' => 0, 'total' => 0, 'label' => 'ارسال‌شده',    'color' => 'indigo'],
            'delivered'   => ['count' => 0, 'total' => 0, 'label' => 'تحویل‌شده',   'color' => 'teal'],
            'paid'        => ['count' => 0, 'total' => 0, 'label' => 'پرداخت‌شده',  'color' => 'cyan'],
            'pending'     => ['count' => 0, 'total' => 0, 'label' => 'در انتظار',    'color' => 'yellow'],
            'cancelled'   => ['count' => 0, 'total' => 0, 'label' => 'لغو‌شده',     'color' => 'red'],
            'refunded'    => ['count' => 0, 'total' => 0, 'label' => 'بازگشت وجه',  'color' => 'orange'],
        ];

        $allOrders = (clone $ordersQuery)
            ->selectRaw('status, COUNT(*) as cnt, SUM(total) as sum_total')
            ->groupBy('status')
            ->get();

        $grandTotal = 0;
        $grandCount = 0;
        foreach ($allOrders as $row) {
            if (isset($financials[$row->status])) {
                $financials[$row->status]['count'] = $row->cnt;
                $financials[$row->status]['total'] = $row->sum_total ?? 0;
            }
            $grandTotal += $row->sum_total ?? 0;
            $grandCount += $row->cnt;
        }

        $recentOrders = (clone $ordersQuery)
            ->with('buyer')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $activeListings = Listing::where('seller_id', $user->id)
            ->whereIn('status', ['active', 'pending', 'scheduled'])
            ->with('category', 'images')
            ->orderBy('ends_at', 'asc')
            ->limit(10)
            ->get();

        $chartData = $this->buildSalesChart($user->id, 7);

        return view('dashboard.seller', compact(
            'stats', 'activeListings', 'recentOrders',
            'chartData', 'financials', 'grandTotal', 'grandCount'
        ));
    }

    public function sellerChartData(\Illuminate\Http\Request $request)
    {
        $user = auth()->user();
        $days = (int) $request->get('days', 7);
        $allowed = [7, 30, 90, 180, 365];
        if (!in_array($days, $allowed)) $days = 7;

        // Must be authenticated seller
        if (!$user || !$user->canSell()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = $this->buildSalesChart($user->id, $days);
        return response()->json($data);
    }

    private function buildSalesChart(int $userId, int $days): array
    {
        $paidStatuses = ['completed', 'processing', 'shipped', 'delivered', 'paid'];
        $labels = [];
        $values = [];

        if ($days <= 30) {
            // Daily grouping with Jalali labels
            for ($i = $days - 1; $i >= 0; $i--) {
                $date = now()->subDays($i);
                // Jalali label: month/day (e.g. فروردین ۱)
                $jalali = \Morilog\Jalali\Jalalian::fromCarbon($date);
                $labels[] = $jalali->getYear() . '/' . $jalali->getMonth() . '/' . $jalali->getDay();
                $values[] = (int) Order::where('seller_id', $userId)
                    ->whereIn('status', $paidStatuses)
                    ->whereDate('created_at', $date->toDateString())
                    ->sum('total');
            }
        } else {
            // Weekly grouping with Jalali labels
            $weeks = (int) ceil($days / 7);
            for ($i = $weeks - 1; $i >= 0; $i--) {
                $start = now()->subWeeks($i)->startOfWeek();
                $jalali = \Morilog\Jalali\Jalalian::fromCarbon($start);
                $labels[] = $jalali->getYear() . '/' . $jalali->getMonth() . '/' . $jalali->getDay();
                $end = now()->subWeeks($i)->endOfWeek();
                $values[] = (int) Order::where('seller_id', $userId)
                    ->whereIn('status', $paidStatuses)
                    ->whereBetween('created_at', [$start, $end])
                    ->sum('total');
            }
        }

        return ['labels' => $labels, 'values' => $values, 'max' => max(array_merge($values, [1]))];
    }

    /**
     * Buyer dashboard
     */
    public function buyerDashboard()
    {
        $user = auth()->user();

        // Get unique active listings where user has bids
        $activeListingsWithBids = Listing::where('status', 'active')
            ->whereHas('bids', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->get();

        // Stats - count unique listings, not total bids
        $activeBidsCount = $activeListingsWithBids->count();

        $wonAuctionsCount = Listing::where('current_winner_id', $user->id)
            ->where('status', 'completed')
            ->count();

        $totalOrdersCount = Order::where('buyer_id', $user->id)->count();

        // My active bids - get latest bid per listing
        $myActiveBids = collect();
        foreach ($activeListingsWithBids->take(5) as $listing) {
            $latestBid = $listing->bids()
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->first();
            
            if ($latestBid) {
                // Check if user is still winning
                $highestBid = $listing->bids()->orderBy('amount', 'desc')->first();
                $latestBid->is_winning = ($highestBid && $highestBid->id === $latestBid->id);
                $latestBid->listing = $listing->load('images');
                $myActiveBids->push($latestBid);
            }
        }

        // Recent orders
        $recentOrders = Order::where('buyer_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Recent activities
        $recentBidActivities = Bid::where('user_id', $user->id)
            ->with('listing')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($bid) {
                return [
                    'type' => 'bid',
                    'icon' => 'gavel',
                    'color' => 'blue',
                    'title' => 'پیشنهاد جدید',
                    'description' => 'پیشنهاد ' . number_format($bid->amount) . ' تومان برای ' . $bid->listing->title,
                    'time' => $bid->created_at,
                ];
            });

        $recentOrderActivities = Order::where('buyer_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($order) {
                return [
                    'type' => 'order',
                    'icon' => 'shopping_bag',
                    'color' => 'green',
                    'title' => 'سفارش جدید',
                    'description' => 'سفارش #' . $order->id . ' به مبلغ ' . number_format($order->total_amount) . ' تومان',
                    'time' => $order->created_at,
                ];
            });

        // Merge and sort activities
        $recentActivities = $recentBidActivities->concat($recentOrderActivities)
            ->sortByDesc('time')
            ->take(10);

        return view('dashboard.buyer-new', compact(
            'activeBidsCount',
            'wonAuctionsCount', 
            'totalOrdersCount',
            'myActiveBids',
            'recentOrders'
        ));
    }
}
