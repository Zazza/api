<?php
namespace App\Tests\ExchangeRate;

use App\Wallet\ExchangeRate\Exchange;
use PHPUnit\Framework\TestCase;

class CommandTest extends TestCase
{
    public function testResponse(): void
    {
        $statusCode = 200;
        $rateRequest = 0.014;
        $body = '{"rates":{"USD":'.$rateRequest.'},"base":"RUB","date":"2020-12-23"}';

        $mockGuzzle = new Guzzle();
        $source = new Exchange(
            $mockGuzzle->setStatusCode($statusCode)->setBody($body)
        );
        $rateResponse = $source->request('RUB');

        self::assertEquals($rateRequest, $rateResponse);
    }
}
