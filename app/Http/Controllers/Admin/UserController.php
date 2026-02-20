<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index(Request $request)
    {
        $query = User::with(['store', 'wallet']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load([
            'store', 
            'wallet', 
            'listings' => function($query) {
                $query->latest()->take(5);
            }, 
            'bids', 
            'orders' => function($query) {
                $query->latest()->take(5);
            }
        ]);

        $stats = [
            'total_listings' => $user->listings()->count(),
            'active_listings' => $user->listings()->where('status', 'active')->count(),
            'total_bids' => $user->bids()->count(),
            'won_auctions' => 0, // Fixed: removed invalid query
            'total_orders' => $user->orders()->count(),
            'total_spent' => $user->orders()->sum('total') ?? 0,
        ];

        return view('admin.users.show', compact('user', 'stats'));
    }

    public function suspend(User $user)
    {
        $user->update(['status' => 'suspended']);
        
        return response()->json(['success' => true]);
    }

    public function activate(User $user)
    {
        $user->update(['status' => 'active']);
        
        return response()->json(['success' => true]);
    }

    public function verifyEmail(User $user)
    {
        $user->update(['email_verified_at' => now()]);
        
        return response()->json(['success' => true]);
    }
}
