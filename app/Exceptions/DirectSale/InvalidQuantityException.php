<?php

namespace App\Exceptions\DirectSale;

use Exception;

class InvalidQuantityException extends Exception
{
    protected $message = 'تعداد درخواستی نامعتبر است.';
    
    public function __construct(int $requested, int $available)
    {
        $this->message = sprintf(
            'تعداد درخواستی (%d) بیشتر از موجودی انبار (%d) است.',
            $requested,
            $available
        );
        parent::__construct($this->message);
    }
}
