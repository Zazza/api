<?php
namespace App\Exception;

use Throwable;

class NegativeBalanceException extends \Exception
{
    public $message = 'Balance cannot be less than 0';
}
