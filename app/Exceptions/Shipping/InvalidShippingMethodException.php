<?php

namespace App\Exceptions\Shipping;

use Exception;

class InvalidShippingMethodException extends Exception
{
    protected $message = 'روش ارسال برای این محصول معتبر نیست.';
    
    public function __construct(int $shippingMethodId, int $listingId)
    {
        $this->message = sprintf(
            'روش ارسال با شناسه %d برای محصول با شناسه %d معتبر نیست.',
            $shippingMethodId,
            $listingId
        );
        parent::__construct($this->message);
    }
}
