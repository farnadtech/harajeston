<?php

namespace App\Exceptions\Cart;

use Exception;

class CartItemNotFoundException extends Exception
{
    protected $message = 'آیتم در سبد خرید یافت نشد.';
    
    public function __construct(int $cartItemId)
    {
        $this->message = sprintf('آیتم با شناسه %d در سبد خرید یافت نشد.', $cartItemId);
        parent::__construct($this->message);
    }
}
