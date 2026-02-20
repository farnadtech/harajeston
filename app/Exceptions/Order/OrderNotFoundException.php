<?php

namespace App\Exceptions\Order;

use Exception;

class OrderNotFoundException extends Exception
{
    protected $message = 'سفارش یافت نشد.';
    
    public function __construct(string $orderNumber)
    {
        $this->message = sprintf('سفارش با شماره %s یافت نشد.', $orderNumber);
        parent::__construct($this->message);
    }
}
