<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderPlacedNotification extends Notification
{
    use Queueable;

    protected $order;
    protected $forSeller;

    public function __construct(Order $order, bool $forSeller = false)
    {
        $this->order = $order;
        $this->forSeller = $forSeller;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $message = $this->forSeller 
            ? 'سفارش جدیدی دریافت کردید.' 
            : 'سفارش شما با موفقیت ثبت شد.';

        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'total' => $this->order->total,
            'message' => $message,
            'type' => 'order_placed',
        ];
    }
}
