<?php

namespace App\Exceptions\Shipping;

use Exception;

class ShippingMethodNotFoundException extends Exception
{
    protected $message = 'روش ارسال یافت نشد.';
    
    public function __construct(int $shippingMethodId)
    {
        $this->message = sprintf('روش ارسال با شناسه %d یافت نشد.', $shippingMethodId);
        parent::__construct($this->message);
    }
}
