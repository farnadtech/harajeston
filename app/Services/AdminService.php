<?php

namespace App\Services;

use App\Models\Listing;
use App\Models\User;
use App\Models\Order;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

class AdminService
{
    /**
     * Get admin dashboard statistics
     * 
     * @return array
     */
    public function getStatistics(): array
    {
        return [
            'active_listings' => $this->getActiveListingsCount(),
            'total_users' => $this->getTotalUsersCount(),
            'transaction_volume' => $this->getTransactionVolume(),
            'total_orders' => $this->getTotalOrdersCount(),
            'pending_orders' => $this->getPendingOrdersCount(),
            'revenue' => $this->getTotalRevenue(),
        ];
    }
    
    /**
     * Get count of active listings (auctions and direct sales)
     * 
     * @return int
     */
    public function getActiveListingsCount(): int
    {
        return Listing::where('status', 'active')->count();
    }
    
    /**
     * Get total users count
     * 
     * @return int
     */
    public function getTotalUsersCount(): int
    {
        return User::count();
    }
    
    /**
     * Get total transaction volume (sum of all wallet transactions)
     * 
     * @return float
     */
    public function getTransactionVolume(): float
    {
        return (float) WalletTransaction::sum('amount');
    }
    
    /**
     * Get total orders count
     * 
     * @return int
     */
    public function getTotalOrdersCount(): int
    {
        return Order::count();
    }
    
    /**
     * Get pending orders count
     * 
     * @return int
     */
    public function getPendingOrdersCount(): int
    {
        return Order::where('status', 'pending')->count();
    }
    
    /**
     * Get total revenue from completed orders
     * 
     * @return float
     */
    public function getTotalRevenue(): float
    {
        return (float) Order::whereIn('status', ['delivered', 'shipped'])
            ->sum('total');
    }
    
    /**
     * Get statistics by date range
     * 
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getStatisticsByDateRange(string $startDate, string $endDate): array
    {
        return [
            'new_users' => User::whereBetween('created_at', [$startDate, $endDate])->count(),
            'new_listings' => Listing::whereBetween('created_at', [$startDate, $endDate])->count(),
            'new_orders' => Order::whereBetween('created_at', [$startDate, $endDate])->count(),
            'revenue' => (float) Order::whereBetween('created_at', [$startDate, $endDate])
                ->whereIn('status', ['delivered', 'shipped'])
                ->sum('total'),
        ];
    }
    
    /**
     * Get top sellers by revenue
     * 
     * @param int $limit
     * @return \Illuminate\Support\Collection
     */
    public function getTopSellers(int $limit = 10)
    {
        return User::where('role', 'seller')
            ->withCount(['listings as total_sales' => function ($query) {
                $query->select(DB::raw('COALESCE(SUM(orders.total), 0)'))
                    ->join('order_items', 'listings.id', '=', 'order_items.listing_id')
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->whereIn('orders.status', ['delivered', 'shipped']);
            }])
            ->orderByDesc('total_sales')
            ->limit($limit)
            ->get();
    }
    
    /**
     * Log admin action
     * 
     * @param User $admin
     * @param string $action
     * @param mixed $target
     * @param array $context
     * @param string|null $reason
     * @return \App\Models\AdminActionLog
     */
    public function logAction(User $admin, string $action, $target = null, array $context = [], ?string $reason = null)
    {
        return \App\Models\AdminActionLog::create([
            'admin_id' => $admin->id,
            'action' => $action,
            'target_type' => $target ? get_class($target) : null,
            'target_id' => $target?->id,
            'context' => $context,
            'reason' => $reason,
            'ip_address' => request()->ip(),
        ]);
    }
    
    /**
     * Get admin action logs with filters
     * 
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getActionLogs(array $filters = [])
    {
        $query = \App\Models\AdminActionLog::with('admin')
            ->orderBy('created_at', 'desc');
        
        if (isset($filters['admin_id'])) {
            $query->where('admin_id', $filters['admin_id']);
        }
        
        if (isset($filters['action'])) {
            $query->where('action', $filters['action']);
        }
        
        if (isset($filters['from'])) {
            $query->whereDate('created_at', '>=', $filters['from']);
        }
        
        if (isset($filters['to'])) {
            $query->whereDate('created_at', '<=', $filters['to']);
        }
        
        return $query->paginate(50);
    }
}
