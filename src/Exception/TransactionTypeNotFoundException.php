<?php
namespace App\Exception;

use Throwable;

class TransactionTypeNotFoundException extends \Exception
{
    public $message = 'No transaction type found: ';

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($this->message . $message, $code, $previous);
    }
}
