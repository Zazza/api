<?php
namespace App\Exception;

use Throwable;

class WalletNotFoundException extends \Exception
{
    public $message = 'No wallet found for id: ';

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($this->message . $message, $code, $previous);
    }
}
