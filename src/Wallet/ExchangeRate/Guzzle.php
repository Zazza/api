<?php
namespace App\Wallet\ExchangeRate;

/**
 * Class Guzzle
 * @package Cryptocoin\Library\Request
 */
class Guzzle implements HttpClient
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';

    /**
     * @param string $url
     * @param string $method
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request(string $url, $method = self::METHOD_GET): string
    {
        $client = new \GuzzleHttp\Client();

        $response = $client->request($method, $url);

        return $response->getBody()->getContents();
    }
}
