<?php

namespace unit\CurrencyProvider;

use blabla\BinLookup\BinlistBinLookup;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;


class BinlistBinLookupTest extends TestCase
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
        $testCountry = 'GB';
        $client = $this->getClient(200, '{"number":{"length":16,"luhn":true},"scheme":"visa","type":"debit","brand":"Traditional","prepaid":null,"country":{"numeric":"826","alpha2":"'
            . $testCountry . '","name":"United Kingdom of Great Britain and Northern Ireland","emoji":"🇬🇧","currency":"GBP","latitude":54,"longitude":-2},"bank":{}}'
        );
        $binLookup = new BinlistBinLookup($client);
        $this->assertEquals(
            $binLookup->getCountryNameByBin(1223),
            $testCountry
        );
    }

    public function testAPIDoesNotHaveSuchBin()
    {
        $client = $this->getClient(404, '');
        $binLookup = new BinlistBinLookup($client);
        $this->expectException(GuzzleException::class);
        $binLookup->getCountryNameByBin(1223);
    }

    public function testAPIGivesMalformedResponse()
    {
        $client = $this->getClient(200, '"hello world!"');
        $binLookup = new BinlistBinLookup($client);
        $this->expectExceptionMessageMatches('/^Malformed.*/');
        $binLookup->getCountryNameByBin(1223);
    }

    public function testCacheUsed()
    {
        $response = new Response(
            200, [], '{"number":{"length":16,"luhn":true},"scheme":"visa","type":"debit","brand":"Traditional","prepaid":null,"country":{"numeric":"826","alpha2":"GB","name":"United Kingdom of Great Britain and Northern Ireland","emoji":"🇬🇧","currency":"GBP","latitude":54,"longitude":-2},"bank":{}}'
        );

        $client = $this->createMock(Client::class);
        $client->expects($this->exactly(1))
            ->method('request')
            ->willReturn($response)
        ;

        $binLookup = new BinlistBinLookup($client);
        $binLookup->getCountryNameByBin(1223);
        $binLookup->getCountryNameByBin(1223);
    }
}
