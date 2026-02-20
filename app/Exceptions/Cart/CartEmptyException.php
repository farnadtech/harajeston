<?php

namespace App\Exceptions\Cart;

use Exception;

class CartEmptyException extends Exception
{
    protected $message = 'سبد خرید خالی است.';
    
    public function __construct()
    {
        parent::__construct($this->message);
    }
}
