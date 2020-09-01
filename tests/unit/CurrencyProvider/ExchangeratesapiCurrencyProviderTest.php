<?php

namespace unit\CurrencyProvider;

use blabla\CurrencyProvider\CurrencyProviderException;
use blabla\CurrencyProvider\ExchangeratesapiCurrencyProvider;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;


class ExchangeratesapiCurrencyProviderTest extends TestCase
{
    protected function getClient($status, $response)
    {
        $mock = new MockHandler([
            new Response($status, [], $response),
        ]);
        $handlerStack = HandlerStack::create($mock);

        return new Client(['handler' => $handlerStack]);
    }

    public function testAPIWorks()
    {
        $testRate = 1.21;
        $client = $this->getClient(200, json_encode(['rates' => ['GBP' => $testRate]]));
        $currencyProvider = new ExchangeratesapiCurrencyProvider($client);
        $this->assertEquals(
            $currencyProvider->getCurrencyRate('GBP'),
            $testRate
        );
    }

    public function testAPIDoesNotHaveSuchCurrencyWorks()
    {
        $testRate = 1.21;
        $client = $this->getClient(200, json_encode(['rates' => ['GBP' => $testRate]]));
        $currencyProvider = new ExchangeratesapiCurrencyProvider($client);

        $this->expectException(CurrencyProviderException::class);
        $currencyProvider->getCurrencyRate('EUR');
    }

    public function testAPIDoesNotWorks()
    {
        $client = $this->getClient(404, '');
        $currencyProvider = new ExchangeratesapiCurrencyProvider($client);
        $this->expectException(GuzzleException::class);
        $currencyProvider->getCurrencyRate('EUR');
    }

    public function testAPIGivesMalformedResponse()
    {
        $client = $this->getClient(200, '"hello world!"');
        $currencyProvider = new ExchangeratesapiCurrencyProvider($client);
        $this->expectExceptionMessageMatches('/^Malformed.*/');
        $currencyProvider->getCurrencyRate('EUR');
    }

    public function testCacheUsed()
    {
        $response = new Response(
            200, [], '{"rates":{"CAD":1.5601,"HKD":9.2536,"ISK":164.3,"PHP":57.837,"DKK":7.4439,"HUF":354.54,"CZK":26.208,"AUD":1.6214,"RON":4.8398,"SEK":10.2888,"IDR":17404.4,"INR":87.4685,"BRL":6.4739,"RUB":88.2993,"HRK":7.528,"JPY":126.47,"THB":37.098,"CHF":1.0774,"SGD":1.6235,"PLN":4.3971,"BGN":1.9558,"TRY":8.777,"CNY":8.1711,"NOK":10.455,"NZD":1.7728,"ZAR":19.9589,"USD":1.194,"MXN":26.059,"ILS":4.0131,"GBP":0.89605,"KRW":1415.76,"MYR":4.9736},"base":"EUR","date":"2020-08-31"}'
        );

        $client = $this->createMock(Client::class);
        $client->expects($this->exactly(1))
            ->method('request')
            ->willReturn($response)
        ;

        $currencyProvider = new ExchangeratesapiCurrencyProvider($client);
        $currencyProvider->getCurrencyRate('GBP');
        $currencyProvider->getCurrencyRate('PHP');
        $currencyProvider->getCurrencyRate('HKD');
    }
}
