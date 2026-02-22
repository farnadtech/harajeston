<?php

namespace App\Notifications;

use App\Models\Listing;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ListingApprovedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Listing $listing
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'آگهی شما تایید شد',
            'message' => sprintf('آگهی "%s" توسط مدیریت تایید و منتشر شد.', $this->listing->title),
            'listing_id' => $this->listing->id,
            'icon' => 'check_circle',
            'color' => 'green',
            'link' => route('listings.show', $this->listing),
        ];
    }
}
