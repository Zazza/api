<?php
namespace App\Wallet;

class Exchange extends Source
{
    const ID = 5;
    const API_URL = 'https://api.exchangeratesapi.io/latest?symbols=USD&base=';
    const KNOWN_CURRENCIES = [
        'RUB'
    ];

    public function request($currency): string
    {
        $guzzle = new Guzzle();
        $response = $guzzle->request(
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
