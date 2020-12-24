<?php
namespace App\Exception;

use Throwable;

class CurrencyNotFoundException extends \Exception
{
    public $message = 'No currency found: ';

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($this->message . $message, $code, $previous);
    }
}
