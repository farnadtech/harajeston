<?php

namespace App\Exceptions\Image;

use Exception;

class ImageSizeTooLargeException extends Exception
{
    protected $message = 'حجم تصویر بیش از حد مجاز است.';
    
    public function __construct(int $sizeInKb, int $maxSizeInKb = 5120)
    {
        $this->message = sprintf(
            'حجم تصویر (%d کیلوبایت) بیش از حد مجاز (%d کیلوبایت) است.',
            $sizeInKb,
            $maxSizeInKb
        );
        parent::__construct($this->message);
    }
}
