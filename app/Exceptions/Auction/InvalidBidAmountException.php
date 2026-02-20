<?php

namespace App\Exceptions\Auction;

use Exception;

class InvalidBidAmountException extends Exception
{
    protected $message = 'مبلغ پیشنهادی نامعتبر است.';
    
    public function __construct(float $bidAmount, float $minimumRequired)
    {
        $this->message = sprintf(
            'مبلغ پیشنهادی (%s ریال) باید بیشتر از حداقل مبلغ مورد نیاز (%s ریال) باشد.',
            number_format($bidAmount),
            number_format($minimumRequired)
        );
        parent::__construct($this->message);
    }
}
