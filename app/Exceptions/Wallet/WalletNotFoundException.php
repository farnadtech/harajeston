<?php

namespace App\Exceptions\Wallet;

use Exception;

class WalletNotFoundException extends Exception
{
    protected $message = 'کیف پول یافت نشد.';
    
    public function __construct(int $userId)
    {
        $this->message = sprintf('کیف پول برای کاربر با شناسه %d یافت نشد.', $userId);
        parent::__construct($this->message);
    }
}
