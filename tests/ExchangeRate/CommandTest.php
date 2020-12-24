<?php
namespace App\Tests\ExchangeRate;

use App\Wallet\ExchangeRate\Exchange;
use PHPUnit\Framework\TestCase;

class CommandTest extends TestCase
{
    public function testResponse(): void
    {
        $statusCode = 200;
        $rate = 0.014;
        $body = '{"rates":{"USD":'.$rate.'},"base":"RUB","date":"2020-12-23"}';

        $mockGuzzle = new Guzzle();
        $source = new Exchange(
            $mockGuzzle->setStatusCode($statusCode)->setBody($body)
        );
        $rate = $source->request('RUB');

        self::assertEquals($rate, $rate);
    }
}
