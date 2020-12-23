<?php
namespace App\Wallet;

use GuzzleHttp\Exception\ClientException;

/**
 * Class Guzzle
 * @package Cryptocoin\Library\Request
 */
class Guzzle
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';

    /** @var array */
    private $headers = [];

    /**
     * Non async request
     * @param string $url
     * @param string $method
     * @return string
     */
    public function request(string $url, $method = self::METHOD_GET)
    {
        $client = new \GuzzleHttp\Client();

        $response = $client->request($method, $url, $this->headers);

        return $response->getBody()->getContents();
    }

    public function setHeader(array $headers)
    {
        $this->headers[array_keys($headers)[0]] = $headers[array_keys($headers)[0]];
    }

    public function setHeaders(array $headers)
    {
        $this->headers = array_merge($this->headers, $headers);
    }
}
