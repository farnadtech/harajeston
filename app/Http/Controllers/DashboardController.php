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

        $stats = [
            'active_auctions' => Listing::where('seller_id', $user->id)
                ->where('status', 'active')
                ->count(),
            'pending_listings' => Listing::where('seller_id', $user->id)
                ->where('status', 'pending')
                ->count(),
            'completed_auctions' => Listing::where('seller_id', $user->id)
                ->where('status', 'completed')
                ->count(),
            'total_sales' => Order::where('seller_id', $user->id)
                ->where('status', 'completed')
                ->sum('total'),
        ];

        $recentOrders = Order::where('seller_id', $user->id)
            ->with('buyer', 'items')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $activeListings = Listing::where('seller_id', $user->id)
            ->where('status', 'active')
            ->with('category', 'images')
            ->orderBy('ends_at', 'asc')
            ->limit(10)
            ->get();

        // Recent activities - combining bids, orders, and listing status changes
        $recentBids = Bid::whereHas('listing', function($q) use ($user) {
                $q->where('seller_id', $user->id);
            })
            ->with('user', 'listing')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($bid) {
                return [
                    'type' => 'bid',
                    'icon' => 'gavel',
                    'color' => 'blue',
                    'title' => 'پیشنهاد جدید',
                    'description' => $bid->user->name . ' پیشنهاد ' . number_format($bid->amount) . ' تومان برای ' . $bid->listing->title,
                    'time' => $bid->created_at,
                ];
            });

        $recentOrderActivities = Order::where('seller_id', $user->id)
            ->with('buyer')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($order) {
                return [
                    'type' => 'order',
                    'icon' => 'shopping_bag',
                    'color' => 'green',
                    'title' => 'سفارش جدید',
                    'description' => 'سفارش #' . $order->order_number . ' از ' . $order->buyer->name,
                    'time' => $order->created_at,
                ];
            });

        // Merge and sort activities
        $recentActivities = $recentBids->concat($recentOrderActivities)
            ->sortByDesc('time')
            ->take(10);

        return view('dashboard.seller', compact('stats', 'activeListings', 'recentOrders', 'recentActivities'));
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
            'recentOrders',
            'recentActivities'
        ));
    }
}
