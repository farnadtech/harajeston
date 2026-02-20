<?php

namespace App\Exceptions\Auction;

use Exception;

class AuctionNotActiveException extends Exception
{
    protected $message = 'مزایده فعال نیست.';
    
    public function __construct(int $listingId, string $currentStatus)
    {
        $this->message = sprintf(
            'مزایده با شناسه %d فعال نیست. وضعیت فعلی: %s',
            $listingId,
            $currentStatus
        );
        parent::__construct($this->message);
    }
}
