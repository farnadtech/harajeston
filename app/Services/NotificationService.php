<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Listing;
use App\Models\Bid;
use App\Models\Order;
use App\Models\Store;

class NotificationService
{
    /**
     * Create a notification for a new bid
     */
    public function notifyNewBid(Bid $bid): void
    {
        $listing = $bid->listing;
        $seller = $listing->seller;
        
        Notification::create([
            'user_id' => $seller->id,
            'type' => 'bid',
            'title' => 'پیشنهاد جدید در مزایده',
            'message' => sprintf(
                'کاربر %s پیشنهاد %s تومان برای "%s" ثبت کرد',
                $bid->user->name,
                number_format($bid->amount),
                $listing->title
            ),
            'icon' => 'gavel',
            'color' => 'blue',
            'link' => $seller->role === 'admin' ? route('admin.listings.show', $listing) : route('listings.show', $listing),
            'is_read' => false,
        ]);
        
        // Notify admin
        $this->notifyAdmins('bid', 'پیشنهاد جدید', sprintf(
            'پیشنهاد %s تومان برای "%s" ثبت شد',
            number_format($bid->amount),
            $listing->title
        ), 'gavel', 'blue', route('admin.listings.show', $listing));
    }
    
    /**
     * Notify previous highest bidder that they've been outbid
     */
    public function notifyOutbid(Bid $newBid, User $previousBidder): void
    {
        $listing = $newBid->listing;
        
        Notification::create([
            'user_id' => $previousBidder->id,
            'type' => 'outbid',
            'title' => 'پیشنهاد بالاتری ثبت شد',
            'message' => sprintf(
                'پیشنهاد %s تومان برای "%s" ثبت شد. پیشنهاد شما دیگر بالاترین پیشنهاد نیست',
                number_format($newBid->amount),
                $listing->title
            ),
            'icon' => 'trending_up',
            'color' => 'orange',
            'link' => route('listings.show', $listing),
            'is_read' => false,
        ]);
    }
    
    /**
     * Create a notification for auction ending soon
     */
    public function notifyAuctionEndingSoon(Listing $listing, int $hoursRemaining): void
    {
        $seller = $listing->seller;
        
        Notification::create([
            'user_id' => $seller->id,
            'type' => 'auction_ending',
            'title' => 'مزایده در حال پایان',
            'message' => sprintf(
                'مزایده "%s" %d ساعت دیگر پایان می‌یابد',
                $listing->title,
                $hoursRemaining
            ),
            'icon' => 'warning',
            'color' => 'yellow',
            'link' => route('listings.show', $listing->id),
            'is_read' => false,
        ]);
        
        // Notify participants
        foreach ($listing->participations as $participation) {
            Notification::create([
                'user_id' => $participation->user_id,
                'type' => 'auction_ending',
                'title' => 'مزایده در حال پایان',
                'message' => sprintf(
                    'مزایده "%s" که در آن شرکت کرده‌اید %d ساعت دیگر پایان می‌یابد',
                    $listing->title,
                    $hoursRemaining
                ),
                'icon' => 'warning',
                'color' => 'yellow',
                'link' => route('listings.show', $listing->id),
                'is_read' => false,
            ]);
        }
    }
    
    /**
     * Create a notification for new order
     */
    public function notifyNewOrder(Order $order): void
    {
        $seller = $order->seller;
        
        Notification::create([
            'user_id' => $seller->id,
            'type' => 'order',
            'title' => 'سفارش جدید',
            'message' => sprintf(
                'سفارش #%s به مبلغ %s تومان ثبت شد',
                $order->order_number,
                number_format($order->total)
            ),
            'icon' => 'shopping_bag',
            'color' => 'green',
            'link' => route('admin.orders.show', $order->id),
            'is_read' => false,
        ]);
        
        // Notify buyer
        Notification::create([
            'user_id' => $order->buyer_id,
            'type' => 'order',
            'title' => 'سفارش شما ثبت شد',
            'message' => sprintf(
                'سفارش #%s به مبلغ %s تومان با موفقیت ثبت شد',
                $order->order_number,
                number_format($order->total)
            ),
            'icon' => 'shopping_bag',
            'color' => 'green',
            'link' => route('orders.show', $order->id),
            'is_read' => false,
        ]);
        
        // Notify admin
        $this->notifyAdmins('order', 'سفارش جدید', sprintf(
            'سفارش #%d به مبلغ %s تومان ثبت شد',
            $order->id,
            number_format($order->total_amount)
        ), 'shopping_bag', 'green', route('admin.orders.show', $order->id));
    }
    
    /**
     * Create a notification for auction won
     */
    public function notifyAuctionWon(Listing $listing, User $winner, int $winningAmount): void
    {
        Notification::create([
            'user_id' => $winner->id,
            'type' => 'auction_won',
            'title' => 'برنده مزایده شدید! 🎉',
            'message' => sprintf(
                'تبریک! شما برنده مزایده "%s" با مبلغ %s تومان شدید. برای تکمیل خرید و پرداخت مبلغ باقیمانده، به صفحه مزایده مراجعه کنید.',
                $listing->title,
                number_format($winningAmount)
            ),
            'icon' => 'celebration',
            'color' => 'green',
            'link' => route('listings.show', $listing->slug),
            'is_read' => false,
        ]);
    }
    
    /**
     * Create a notification for payment received
     */
    public function notifyPaymentReceived(User $user, float $amount): void
    {
        Notification::create([
            'user_id' => $user->id,
            'type' => 'payment',
            'title' => 'پرداخت دریافت شد',
            'message' => sprintf(
                'مبلغ %s تومان به کیف پول شما واریز شد',
                number_format($amount)
            ),
            'icon' => 'payments',
            'color' => 'green',
            'link' => route('wallet.show'),
            'is_read' => false,
        ]);
    }
    
    /**
     * Create a notification for new user registration
     */
    public function notifyNewUser(User $user): void
    {
        $this->notifyAdmins('user', 'کاربر جدید', sprintf(
            'کاربر جدیدی با نام "%s" ثبت‌نام کرد',
            $user->name
        ), 'person_add', 'blue', route('admin.users.show', $user->id));
    }
    
    /**
     * Create a notification for seller approval
     */
    public function notifySellerApproved(User $user): void
    {
        Notification::create([
            'user_id' => $user->id,
            'type' => 'seller_approved',
            'title' => 'درخواست فروشندگی تایید شد',
            'message' => 'تبریک! درخواست فروشندگی شما تایید شد و اکنون می‌توانید محصولات خود را اضافه کنید',
            'icon' => 'check_circle',
            'color' => 'green',
            'link' => route('dashboard'),
            'is_read' => false,
        ]);
        
        $this->notifyAdmins('seller', 'فروشنده تایید شد', sprintf(
            'درخواست فروشندگی کاربر "%s" تایید شد',
            $user->name
        ), 'check_circle', 'green', route('admin.sellers.show', $user->id));
    }
    
    /**
     * Create a notification for seller rejection
     */
    public function notifySellerRejected(User $user, string $reason): void
    {
        Notification::create([
            'user_id' => $user->id,
            'type' => 'seller_rejected',
            'title' => 'درخواست فروشندگی رد شد',
            'message' => sprintf('درخواست فروشندگی شما رد شد. دلیل: %s', $reason),
            'icon' => 'cancel',
            'color' => 'red',
            'link' => route('seller-request.create'),
            'is_read' => false,
        ]);
        
        $this->notifyAdmins('seller', 'فروشنده رد شد', sprintf(
            'درخواست فروشندگی کاربر "%s" رد شد. دلیل: %s',
            $user->name,
            $reason
        ), 'cancel', 'red', route('admin.sellers.show', $user->id));
    }
    
    /**
     * Create a notification for seller suspension
     */
    public function notifySellerSuspended(User $user, string $reason): void
    {
        Notification::create([
            'user_id' => $user->id,
            'type' => 'seller_suspended',
            'title' => 'حساب فروشندگی شما تعلیق شد',
            'message' => sprintf('حساب فروشندگی شما تعلیق شد. دلیل: %s', $reason),
            'icon' => 'block',
            'color' => 'orange',
            'link' => route('dashboard'),
            'is_read' => false,
        ]);
        
        $this->notifyAdmins('seller', 'فروشنده تعلیق شد', sprintf(
            'حساب فروشندگی کاربر "%s" تعلیق شد. دلیل: %s',
            $user->name,
            $reason
        ), 'block', 'orange', route('admin.sellers.show', $user->id));
    }
    
    /**
     * Create a notification for seller reactivation
     */
    public function notifySellerReactivated(User $user): void
    {
        Notification::create([
            'user_id' => $user->id,
            'type' => 'seller_reactivated',
            'title' => 'حساب فروشندگی شما فعال شد',
            'message' => 'حساب فروشندگی شما مجدداً فعال شد و می‌توانید محصولات خود را اضافه کنید',
            'icon' => 'check_circle',
            'color' => 'green',
            'link' => route('dashboard'),
            'is_read' => false,
        ]);
        
        $this->notifyAdmins('seller', 'فروشنده فعال شد', sprintf(
            'حساب فروشندگی کاربر "%s" فعال شد',
            $user->name
        ), 'check_circle', 'green', route('admin.sellers.show', $user->id));
    }
    
    /**
     * Create a notification for new listing
     */
    public function notifyNewListing(Listing $listing): void
    {
        $seller = $listing->seller;
        
        Notification::create([
            'user_id' => $seller->id,
            'type' => 'listing',
            'title' => 'آگهی جدید ایجاد شد',
            'message' => sprintf('آگهی "%s" با موفقیت ایجاد شد', $listing->title),
            'icon' => 'add',
            'color' => 'blue',
            'link' => route('listings.show', $listing->id),
            'is_read' => false,
        ]);
        
        $this->notifyAdmins('listing', 'آگهی جدید', sprintf(
            'آگهی "%s" توسط "%s" ایجاد شد',
            $listing->title,
            $seller->name
        ), 'add', 'blue', route('admin.listings.show', $listing->id));
    }
    
    /**
     * Create a notification for listing approval
     */
    public function notifyListingApproved(Listing $listing): void
    {
        $seller = $listing->seller;
        
        Notification::create([
            'user_id' => $seller->id,
            'type' => 'listing_approved',
            'title' => 'آگهی تایید شد',
            'message' => sprintf('آگهی "%s" تایید شد و در سایت نمایش داده می‌شود', $listing->title),
            'icon' => 'check_circle',
            'color' => 'green',
            'link' => route('listings.show', $listing->id),
            'is_read' => false,
        ]);
    }
    
    /**
     * Create a notification for listing rejection
     */
    public function notifyListingRejected(Listing $listing, string $reason): void
    {
        $seller = $listing->seller;
        
        Notification::create([
            'user_id' => $seller->id,
            'type' => 'listing_rejected',
            'title' => 'آگهی رد شد',
            'message' => sprintf('آگهی "%s" رد شد. دلیل: %s', $listing->title, $reason),
            'icon' => 'cancel',
            'color' => 'red',
            'link' => route('listings.show', $listing->id),
            'is_read' => false,
        ]);
    }
    
    /**
     * Create a notification for order status update
     */
    public function notifyOrderStatusUpdated(Order $order, string $oldStatus, string $newStatus): void
    {
        // Notify buyer
        Notification::create([
            'user_id' => $order->buyer_id,
            'type' => 'order_status',
            'title' => 'تغییر وضعیت سفارش',
            'message' => sprintf('وضعیت سفارش #%d از "%s" به "%s" تغییر کرد', 
                $order->id, 
                $this->getOrderStatusLabel($oldStatus),
                $this->getOrderStatusLabel($newStatus)
            ),
            'icon' => 'update',
            'color' => 'blue',
            'link' => route('orders.show', $order->id),
            'is_read' => false,
        ]);
        
        // Notify seller
        Notification::create([
            'user_id' => $order->seller_id,
            'type' => 'order_status',
            'title' => 'تغییر وضعیت سفارش',
            'message' => sprintf('وضعیت سفارش #%d از "%s" به "%s" تغییر کرد', 
                $order->id, 
                $this->getOrderStatusLabel($oldStatus),
                $this->getOrderStatusLabel($newStatus)
            ),
            'icon' => 'update',
            'color' => 'blue',
            'link' => route('admin.orders.show', $order->id),
            'is_read' => false,
        ]);
    }
    
    /**
     * Helper method to get order status labels
     */
    private function getOrderStatusLabel(string $status): string
    {
        $labels = [
            'pending' => 'در انتظار پرداخت',
            'paid' => 'پرداخت شده',
            'processing' => 'در حال پردازش',
            'shipped' => 'ارسال شده',
            'delivered' => 'تحویل داده شده',
            'cancelled' => 'لغو شده',
            'refunded' => 'مرجوع شده',
        ];
        
        return $labels[$status] ?? $status;
    }
    
    /**
     * Notify all admins
     */
    private function notifyAdmins(string $type, string $title, string $message, string $icon, string $color, ?string $link = null): void
    {
        $admins = User::where('role', 'admin')->get();
        
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'icon' => $icon,
                'color' => $color,
                'link' => $link,
                'is_read' => false,
            ]);
        }
    }
}
