<?php

namespace App\Wallet;

class Source
{
    /** @var int */
    protected $request;

    /**
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
