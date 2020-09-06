<?php
declare(strict_types=1);

namespace unit\CurrencyConverter;

use blabla\CurrencyConverter\CurrencyConverterException;
use blabla\CurrencyProvider\CurrencyProvider;
use PHPUnit\Framework\TestCase;
use blabla\CurrencyConverter\SimpleToEuroCurrencyConverter;

class SimpleToEuroCurrencyConverterTest extends TestCase
{

    public function testConverterCouldNotConvertToNotEuro()
    {
        $currencyProvider = $this->createMock(CurrencyProvider::class);
        $converter = new SimpleToEuroCurrencyConverter($currencyProvider);

        $this->expectException(CurrencyConverterException::class);
        $converter->convert(12, SimpleToEuroCurrencyConverter::EURO_CURRENCY_NAME, 'RUB');
    }

    public function testCurrencyProviderNotUsedIfConvertFromEuro()
    {
        $currencyProvider = $this->createMock(CurrencyProvider::class);
        $currencyProvider
            ->expects($this->exactly(0))
            ->method('getCurrencyRate')
        ;

        $converter = new SimpleToEuroCurrencyConverter($currencyProvider);
        $converter->convert(12.01, SimpleToEuroCurrencyConverter::EURO_CURRENCY_NAME);
    }

    public function convertCorrectlyProvider()
    {
        return [
            [12.01, 12.01, SimpleToEuroCurrencyConverter::EURO_CURRENCY_NAME, 1.],
            [41.8760469012, 50.00, 'USD', 1.194],
            [79.0701352099, 10000.00, 'JPY', 126.47],
            [108.877721943, 130.00, 'USD', 1.194],
            [2232.01830255, 2000.00, 'GBP', 0.89605],
        ];
    }

    /**
     * @dataProvider convertCorrectlyProvider
     */
    public function testConvertCorrectly($expected, $amount, $currencyFrom, $rate)
    {
        $currencyProvider = $this->createMock(CurrencyProvider::class);
        $currencyProvider->method('getCurrencyRate')->willReturn($rate);

        $converter = new SimpleToEuroCurrencyConverter($currencyProvider);

        $this->assertEquals($expected, $converter->convert($amount, $currencyFrom));
    }
}