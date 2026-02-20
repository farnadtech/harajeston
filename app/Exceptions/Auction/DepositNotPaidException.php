<?php

namespace App\Exceptions\Auction;

use Exception;

class DepositNotPaidException extends Exception
{
    protected $message = 'سپرده مزایده پرداخت نشده است.';
    
    public function __construct(int $listingId, int $userId)
    {
        $this->message = sprintf(
            'کاربر با شناسه %d سپرده مزایده با شناسه %d را پرداخت نکرده است.',
            $userId,
            $listingId
        );
        parent::__construct($this->message);
    }
}
