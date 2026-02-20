<?php

namespace App\Notifications;

use App\Models\Listing;
use App\Models\Bid;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OutbidNotification extends Notification
{
    use Queueable;

    protected $listing;
    protected $newBid;

    public function __construct(Listing $listing, Bid $newBid)
    {
        $this->listing = $listing;
        $this->newBid = $newBid;
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
            'new_bid_amount' => $this->newBid->amount,
            'message' => 'پیشنهاد شما در مزایده پیشی گرفته شد.',
            'type' => 'outbid',
        ];
    }
}
