<?php
namespace App\Wallet\ExchangeRate;

interface HttpClient
{
    public function request(string $url, string $method);
}
