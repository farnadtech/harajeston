<?php

namespace App\Exceptions\Wallet;

use Exception;

class InsufficientBalanceException extends Exception
{
    protected $message = 'موجودی کیف پول کافی نیست.';
    
    public function __construct(float $required, float $available)
    {
        $this->message = sprintf(
            'موجودی کیف پول کافی نیست. مبلغ مورد نیاز: %s ریال، موجودی در دسترس: %s ریال',
            number_format($required),
            number_format($available)
        );
        parent::__construct($this->message);
    }
}
