<?php
namespace App\Exception;

use Throwable;

class TransactionReasonNotFoundException extends \Exception
{
    public $message = 'No transaction reason found: ';

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($this->message . $message, $code, $previous);
    }
}
