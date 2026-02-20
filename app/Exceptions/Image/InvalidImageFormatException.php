<?php

namespace App\Exceptions\Image;

use Exception;

class InvalidImageFormatException extends Exception
{
    protected $message = 'فرمت تصویر نامعتبر است.';
    
    public function __construct(string $format, array $allowedFormats = ['jpg', 'jpeg', 'png', 'webp'])
    {
        $this->message = sprintf(
            'فرمت تصویر "%s" نامعتبر است. فرمت‌های مجاز: %s',
            $format,
            implode('، ', $allowedFormats)
        );
        parent::__construct($this->message);
    }
}
