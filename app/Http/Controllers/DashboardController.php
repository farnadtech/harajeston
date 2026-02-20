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
        } elseif ($user->role === 'seller') {
            return $this->sellerDashboard();
        } else {
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
            'total_listings' => Listing::where('seller_id', $user->id)->count(),
            'active_auctions' => Listing::where('seller_id', $user->id)
                ->where('type', 'auction')
                ->where('status', 'active')
                ->count(),
            'active_sales' => Listing::where('seller_id', $user->id)
                ->whereIn('type', ['direct_sale', 'hybrid'])
                ->where('status', 'active')
                ->count(),
            'total_orders' => Order::where('seller_id', $user->id)->count(),
            'pending_orders' => Order::where('seller_id', $user->id)
                ->where('status', 'pending')
                ->count(),
        ];

        $recentOrders = Order::where('seller_id', $user->id)
            ->with('buyer', 'items')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $lowStockListings = Listing::forSeller($user->id)
            ->lowStock()
            ->withRelations()
            ->get();

        return view('dashboard.seller', compact('stats', 'recentOrders', 'lowStockListings'));
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
            'total_orders' => Order::where('buyer_id', $user->id)->count(),
            'pending_orders' => Order::where('buyer_id', $user->id)
                ->where('status', 'pending')
                ->count(),
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
