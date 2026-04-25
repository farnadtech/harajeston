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
            ? 'سفارش جدید دریافت شد' 
            : 'ثبت سفارش';
            
        if ($this->forSeller) {
            $addressParts = array_filter([
                $this->order->shipping_city,
                $this->order->shipping_address,
                $this->order->shipping_postal_code ? 'کد پستی: '.$this->order->shipping_postal_code : null,
                $this->order->shipping_phone ? 'تلفن: '.$this->order->shipping_phone : null,
            ]);
            $address = implode('، ', $addressParts);
            $message = sprintf(
                'سفارش جدیدی با شماره %s به مبلغ %s تومان دریافت کردید.%s',
                $this->order->order_number,
                number_format($this->order->total),
                $address ? ' | آدرس: ' . $address : ''
            );
        } else {
            $message = sprintf('سفارش شما با شماره %s با موفقیت ثبت شد.', $this->order->order_number);
        }

        return [
            'title' => $title,
            'message' => $message,
            'icon' => 'shopping_bag',
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
