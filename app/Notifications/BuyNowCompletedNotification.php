<?php

namespace App\Notifications;

use App\Models\Order;
use App\Models\Listing;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BuyNowCompletedNotification extends Notification
{
    use Queueable;

    protected $order;
    protected $listing;

    public function __construct(Order $order, Listing $listing)
    {
        $this->order = $order;
        $this->listing = $listing;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'خرید فوری انجام شد',
            'message' => sprintf(
                'حراجی "%s" با خرید فوری به پایان رسید. خریدار در حال وارد کردن آدرس ارسال است.',
                $this->listing->title
            ),
            'icon' => 'shopping_bag',
            'color' => 'orange',
            'link' => route('orders.show', $this->order->id),
            'order_id' => $this->order->id,
            'listing_id' => $this->listing->id,
        ];
    }

    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'listing_id' => $this->listing->id,
        ];
    }
}
