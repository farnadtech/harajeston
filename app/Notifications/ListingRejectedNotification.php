<?php

namespace App\Notifications;

use App\Models\Listing;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ListingRejectedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Listing $listing,
        public string $reason
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'آگهی شما رد شد',
            'message' => sprintf('آگهی "%s" توسط مدیریت رد شد. دلیل: %s', $this->listing->title, $this->reason),
            'listing_id' => $this->listing->id,
            'icon' => 'cancel',
            'color' => 'red',
            'link' => route('dashboard'),
        ];
    }
}
