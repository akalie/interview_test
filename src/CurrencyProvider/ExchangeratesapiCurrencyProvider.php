<?php declare(strict_types=1);


namespace blabla\CurrencyProvider;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ExchangeratesapiCurrencyProvider implements CurrencyProvider
{
    protected const API_URL = 'https://api.exchangeratesapi.io/latest';

    protected Client $client;
    protected array $listCache = [];

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $currencyName presumably ISO 4217 (https://en.wikipedia.org/wiki/ISO_4217)
     *
     * @return float
     * @throws CurrencyProviderException
     * @throws GuzzleException
     */
    public function getCurrencyRate(string $currencyName): float
    {
        $list = $this->getCurrencyToRateList();

        if (!isset($list[$currencyName])) {
            throw new CurrencyProviderException(
                'Failed to find currency "' . $currencyName
                . '" in exchangeratesapi rates list'
            );
        }

        return (float)$list[$currencyName];
    }

    /**
     * @throws CurrencyProviderException
     * @throws GuzzleException
     */
    protected function getCurrencyToRateList(): array
    {
        if (!empty($this->listCache)) {
            return $this->listCache;
        }

        $response = $this->client->request('GET', static::API_URL);

        $apiResponse = json_decode(
            $response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR
        );

        if (!isset($apiResponse['rates']) || !is_array($apiResponse['rates'])) {
            throw new CurrencyProviderException(
                'Malformed exchangeratesapi rates response'
            );
        }

        return $this->listCache = $apiResponse['rates'];
    }
}