<?php

namespace App\Notifications;

use App\Models\Listing;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DepositReleasedNotification extends Notification
{
    use Queueable;

    protected $listing;
    protected $amount;

    public function __construct(Listing $listing, float $amount)
    {
        $this->listing = $listing;
        $this->amount = $amount;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'listing_id' => $this->listing->id,
            'title' => $this->listing->title,
            'amount' => $this->amount,
            'message' => 'سپرده شما آزاد شد.',
            'type' => 'deposit_released',
        ];
    }
}
