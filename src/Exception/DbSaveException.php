<?php
namespace App\Exception;

class DbSaveException extends \Exception
{
    public $message = 'DB error';
}
