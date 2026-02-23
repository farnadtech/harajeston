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
        // Statistics
        $stats = [
            'total_sales' => Order::where('status', 'completed')->sum('total'),
            'active_auctions' => Listing::where('status', 'active')->count(),
            'active_users' => User::where('role', '!=', 'admin')->whereHas('bids', function($q) {
                $q->where('created_at', '>=', now()->subDays(30));
            })->count(),
            'pending_approvals' => Listing::where('status', 'pending')->count() + 
                                   User::where('seller_status', 'pending')->count(),
        ];

        // Pending sellers (for approval widget) - REAL pending sellers
        $pendingSellers = User::where('seller_status', 'pending')
            ->with('store')
            ->orderBy('seller_requested_at', 'desc')
            ->take(4)
            ->get();

        // Recent listings
        $recentListings = Listing::with(['seller.store', 'images'])
            ->latest()
            ->paginate(10);

        return view('admin.dashboard', compact('stats', 'pendingSellers', 'recentListings'));
    }
}
