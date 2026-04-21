<?php

namespace App\Notifications;

use App\Models\Listing;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ListingSuspendedNotification extends Notification
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
            'title' => 'آگهی شما تعلیق شد',
            'message' => sprintf('آگهی "%s" توسط مدیریت تعلیق شد. دلیل: %s', $this->listing->title, $this->reason),
            'listing_id' => $this->listing->id,
            'icon' => 'block',
            'color' => 'red',
            'link' => route('listings.edit', $this->listing),
        ];
    }
}
