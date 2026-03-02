<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderReadyForProcessingNotification extends Notification
{
    use Queueable;

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'سفارش آماده پردازش',
            'message' => sprintf(
                'خریدار آدرس ارسال سفارش %s را وارد کرد. سفارش آماده پردازش و ارسال است.',
                $this->order->order_number
            ),
            'icon' => 'local_shipping',
            'color' => 'green',
            'link' => route('orders.show', $this->order->id),
            'order_id' => $this->order->id,
        ];
    }

    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->order->id,
        ];
    }
}
