<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Listing;
use App\Models\Bid;
use App\Models\Order;

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
            'link' => route('admin.listings.show', $listing->id),
            'is_read' => false,
        ]);
        
        // Notify admin
        $this->notifyAdmins('bid', 'پیشنهاد جدید', sprintf(
            'پیشنهاد %s تومان برای "%s" ثبت شد',
            number_format($bid->amount),
            $listing->title
        ), 'gavel', 'blue', route('admin.listings.show', $listing->id));
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
                'سفارش #%d به مبلغ %s تومان ثبت شد',
                $order->id,
                number_format($order->total_amount)
            ),
            'icon' => 'shopping_bag',
            'color' => 'green',
            'link' => route('admin.orders.show', $order->id),
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
    public function notifyAuctionWon(Listing $listing, User $winner): void
    {
        Notification::create([
            'user_id' => $winner->id,
            'type' => 'auction_won',
            'title' => 'برنده مزایده شدید!',
            'message' => sprintf(
                'شما برنده مزایده "%s" شدید. لطفا برای نهایی‌سازی خرید اقدام کنید',
                $listing->title
            ),
            'icon' => 'celebration',
            'color' => 'green',
            'link' => route('listings.show', $listing->id),
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
