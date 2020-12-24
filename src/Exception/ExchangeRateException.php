<?php
namespace App\Exception;

use Throwable;

class ExchangeRateException extends \Exception
{
    public $message = 'Exchange rate not found for currency: ';

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($this->message . $message, $code, $previous);
    }
}
