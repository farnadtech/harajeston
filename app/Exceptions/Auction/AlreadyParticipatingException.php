<?php

namespace App\Exceptions\Auction;

use Exception;

class AlreadyParticipatingException extends Exception
{
    protected $message = 'شما قبلاً در این مزایده شرکت کرده‌اید.';
    
    public function __construct(int $listingId, int $userId)
    {
        $this->message = sprintf(
            'کاربر با شناسه %d قبلاً در مزایده با شناسه %d شرکت کرده است.',
            $userId,
            $listingId
        );
        parent::__construct($this->message);
    }
}
