<?php
namespace App\Wallet\ExchangeRate;

/**
 * Class Exchange
 * API for exchangeratesapi.io
 * @package App\Wallet\Source
 */
class Exchange extends Common
{
    const ID = 5;
    const API_URL = 'https://api.exchangeratesapi.io/latest?symbols=USD&base=';
    const KNOWN_CURRENCIES = [
        'RUB'
    ];

    /**
     * @param $currency
     * @return string
     */
    public function request($currency): string
    {
        $response = $this->guzzle->request(
            self::API_URL . $currency,
            Guzzle::METHOD_GET
        );

        $responseDecode = $this->decode($response);
        if (!$responseDecode) return false;

        if (array_key_exists('rates', $responseDecode)) {
            if (array_key_exists('USD', $responseDecode['rates'])) {
                return $responseDecode['rates']['USD'];
            }
        }
    }
}
