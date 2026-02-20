<?php

namespace App\Exceptions\DirectSale;

use Exception;

class OutOfStockException extends Exception
{
    protected $message = 'محصول موجود نیست.';
    
    public function __construct(int $listingId, string $title = null)
    {
        if ($title) {
            $this->message = sprintf('محصول "%s" موجود نیست.', $title);
        } else {
            $this->message = sprintf('محصول با شناسه %d موجود نیست.', $listingId);
        }
        parent::__construct($this->message);
    }
}
