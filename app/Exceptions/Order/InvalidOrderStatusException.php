<?php

namespace App\Exceptions\Order;

use Exception;

class InvalidOrderStatusException extends Exception
{
    protected $message = 'تغییر وضعیت سفارش مجاز نیست.';
    
    public function __construct(string $currentStatus, string $newStatus)
    {
        $this->message = sprintf(
            'تغییر وضعیت سفارش از "%s" به "%s" مجاز نیست.',
            $currentStatus,
            $newStatus
        );
        parent::__construct($this->message);
    }
}
