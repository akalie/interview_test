<?php declare(strict_types=1);


namespace blabla\BinLookup;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class BinlistBinLookup implements BinLookup
{
    const API_URL = 'https://lookup.binlist.net/';

    protected Client $client;
    protected array $binListCache = [];

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param int $bin Bank Identification Number (https://en.wikipedia.org/wiki/Payment_card_number#Issuer_identification_number_(IIN))
     *
     * @return string country name in 2-letters format alpha-2 (https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2)
     * @throws BinLookupException
     * @throws GuzzleException
     */
    public function getCountryNameByBin(int $bin): string
    {
        if (isset($this->binListCache[$bin])) {
            return $this->binListCache[$bin];
        }

        $countryCode = $this->getCountryCodeByBin($bin);

        return $this->binListCache[$bin] = $countryCode;
    }

    /**
     * @throws BinLookupException
     * @throws GuzzleException
     */
    protected function getCountryCodeByBin(int $bin): string
    {
        $response = $this->client->request('GET', static::API_URL . $bin);

        $apiResponse = json_decode(
            $response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR
        );
        if (!isset($apiResponse['country']['alpha2'])) {
            throw new BinLookupException(
                'Malformed lookup.binlist.net response for bin ' . $bin
            );
        }

        return $apiResponse['country']['alpha2'];
    }
}