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

    public function toDatabase(object $notifiable): array
    {
        $title = $this->forSeller 
            ? 'سفارش جدید' 
            : 'ثبت سفارش';
            
        $message = $this->forSeller 
            ? sprintf('سفارش جدیدی با شماره %s دریافت کردید.', $this->order->order_number)
            : sprintf('سفارش شما با شماره %s با موفقیت ثبت شد.', $this->order->order_number);

        return [
            'title' => $title,
            'message' => $message,
            'icon' => 'shopping-bag',
            'color' => 'green',
            'link' => route('orders.show', $this->order->id),
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'total' => $this->order->total,
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
