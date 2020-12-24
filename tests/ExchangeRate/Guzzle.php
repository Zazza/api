<?php
namespace App\Tests\ExchangeRate;

use App\Wallet\ExchangeRate\HttpClient;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class Guzzle implements HttpClient
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';

    /**
     * @var int
     */
    private int $status;

    /**
     * @var string
     */
    private string $body;

    /**
     * @param int $status
     * @return $this
     */
    public function setStatusCode(int $status): Guzzle
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param string $body
     * @return $this
     */
    public function setBody(string $body): Guzzle
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @param string $url
     * @param string $method
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request(string $url, $method = self::METHOD_GET): string
    {
        $mock = new MockHandler([new Response($this->status, [], $this->body)]);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        return $client
            ->request($url, $method)
            ->getBody()
            ->getContents();
    }
}
