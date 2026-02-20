<?php

namespace App\Exceptions\Payment;

use Exception;

class PaymentFailedException extends Exception
{
    protected $message = 'پرداخت ناموفق بود.';
    
    public function __construct(string $reason = null)
    {
        if ($reason) {
            $this->message = sprintf('پرداخت ناموفق بود. دلیل: %s', $reason);
        }
        parent::__construct($this->message);
    }
}
