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

        return view('dashboard.seller-new', compact('stats', 'activeListings', 'recentOrders'));
    }

    /**
     * Buyer dashboard
     */
    public function buyerDashboard()
    {
        $user = auth()->user();

        $stats = [
            'active_bids' => Bid::where('user_id', $user->id)
                ->whereHas('listing', function ($q) {
                    $q->where('status', 'active');
                })
                ->count(),
            'recent_purchases' => Order::where('buyer_id', $user->id)
                ->where('created_at', '>=', now()->subDays(30))
                ->count(),
            'frozen_deposits' => $user->wallet->frozen ?? 0,
        ];

        $activeBids = Bid::where('user_id', $user->id)
            ->whereHas('listing', function ($q) {
                $q->where('status', 'active');
            })
            ->with('listing')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recentOrders = Order::where('buyer_id', $user->id)
            ->with('seller', 'items')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard.buyer', compact('stats', 'activeBids', 'recentOrders'));
    }
}
