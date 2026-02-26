<?php

namespace App\Notifications;

use App\Models\Listing;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ListingChangesRejectedNotification extends Notification
{
    use Queueable;

    protected $listing;
    protected $reason;

    public function __construct(Listing $listing, string $reason)
    {
        $this->listing = $listing;
        $this->reason = $reason;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'رد تغییرات آگهی',
            'message' => 'تغییرات آگهی "' . $this->listing->title . '" توسط ادمین رد شد. دلیل: ' . $this->reason,
            'listing_id' => $this->listing->id,
            'listing_slug' => $this->listing->slug,
            'type' => 'listing_changes_rejected',
            'icon' => 'cancel',
            'color' => 'red'
        ];
    }
}
