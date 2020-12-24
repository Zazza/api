<?php

namespace App\Wallet\ExchangeRate;

/**
 * Class Common
 * @package App\Wallet\Source
 */
class Common
{
    /**
     * @var HttpClient
     */
    protected HttpClient $guzzle;

    /**
     * @var int
     */
    protected $request;

    public function __construct(HttpClient $guzzle)
    {
        $this->guzzle = $guzzle;
    }

    /**
     * Json decode
     * @param $response
     * @return bool|mixed
     */
    protected function decode($response)
    {
        try {
            $result = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return false;
        }

        return $result;
    }
}
