<?php

namespace App\Notifications;

use App\Models\Listing;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WinnerSelectedNotification extends Notification
{
    use Queueable;

    protected $listing;

    public function __construct(Listing $listing)
    {
        $this->listing = $listing;
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
            'winning_bid' => $this->listing->current_highest_bid,
            'message' => 'تبریک! شما برنده مزایده شدید.',
            'type' => 'winner_selected',
        ];
    }
}
