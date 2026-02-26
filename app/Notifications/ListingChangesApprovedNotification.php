<?php

namespace App\Notifications;

use App\Models\Listing;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ListingChangesApprovedNotification extends Notification
{
    use Queueable;

    protected $listing;

    public function __construct(Listing $listing)
    {
        $this->listing = $listing;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'تایید تغییرات آگهی',
            'message' => 'تغییرات آگهی "' . $this->listing->title . '" توسط ادمین تایید و اعمال شد.',
            'listing_id' => $this->listing->id,
            'listing_slug' => $this->listing->slug,
            'type' => 'listing_changes_approved',
            'icon' => 'check_circle',
            'color' => 'green'
        ];
    }
}
