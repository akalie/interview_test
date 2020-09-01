<?php


namespace unit\CurrencyProvider;


use blabla\BinLookup\BinLookup;
use blabla\CommissionCalculator\SimpleCommissionCalculator;
use blabla\CurrencyProvider\CurrencyProvider;
use blabla\TransactionInfo;
use PHPUnit\Framework\TestCase;

class SimpleCommissionCalculatorTest extends TestCase
{

    public function isEuropeCountryCheckProvider(): array
    {
        return [
            ['BG', true],
            ['BY', false],
        ];
    }

    /**
     * @dataProvider isEuropeCountryCheckProvider
     */
    public function testIsEuropeCountryCheck(string $country, bool $result)
    {
        $calculator = new SimpleCommissionCalculator(
            $this->createMock(BinLookup::class),
            $this->createMock(CurrencyProvider::class),
        );

        $this->assertEquals($result, $calculator->isEuropeCountry($country));
    }

    public function commissionMultiplierCalculatingProvider(): array
    {
        return [
            ['BG', SimpleCommissionCalculator::EUROPE_COMMISSION_MULTIPLIER],
            ['BY', SimpleCommissionCalculator::NON_EUROPE_COMMISSION_MULTIPLIER],
        ];
    }

    /**
     * @dataProvider commissionMultiplierCalculatingProvider
     */
    public function testCommissionMultiplierCalculating(string $country, float $result)
    {
        $calculator = new SimpleCommissionCalculator(
            $this->createMock(BinLookup::class),
            $this->createMock(CurrencyProvider::class)
        );

        $this->assertEquals($result, $calculator->getCommissionMultiplier($country));
    }


    public function ceilingProvider(): array
    {
        return [
            [0.01, 0.000001],
            [0.03, 0.02999999],
            [1111111.51, 1111111.505],
            [3.64, 3.6333333333333],
            [3.60, 3.6],
            [2.22, 2.22],
            [0.47, 0.46180],
            [0.47, 0.46180231],
        ];
    }

    /**
     * @dataProvider ceilingProvider
     */
    public function testCeiling(float $expected, float $dataToCeil)
    {
        $calculator = new SimpleCommissionCalculator(
            $this->createMock(BinLookup::class),
            $this->createMock(CurrencyProvider::class)
        );

        $this->assertEquals($expected, $calculator->ceil($dataToCeil));
    }

    /**
     * ~ 2020-09-01 12:00:00
     */
    public function commissionCalculationProvider(): array
    {
        return [
            [
                'expectedResult' => 1,
                'binData' => 'DK',
                'currencyData' => 7.4439,
                'transactionData' => ['bin' => 45717360, 'amount' => 100.00,'currency' => 'EUR'],
            ],
            [
                'expectedResult' => 0.41876046901173,
                'binData' => 'LT',
                'currencyData' => 1.194,
                'transactionData' => ['bin' => 516793, 'amount' => 50.00,'currency' => 'USD'],
            ],
            [
                'expectedResult' => 1.5814027041986,
                'binData' => 'JP',
                'currencyData' => 126.47,
                'transactionData' => ['bin' => 45417360, 'amount' => 10000.00,'currency' => 'JPY'],
            ],
            [
                'expectedResult' => 2.177554438861,
                'binData' => 'US',
                'currencyData' => 1.194,
                'transactionData' => ['bin' => 41417360, 'amount' => 130.00,'currency' => 'USD'],
            ],
            [
                'expectedResult' => 44.640366051002,
                'binData' => 'GB',
                'currencyData' => 0.89605,
                'transactionData' => ['bin' => 4745030, 'amount' => 2000.00,'currency' => 'GBP'],
            ],
        ];
    }

    /**
     * @dataProvider commissionCalculationProvider
     */
    public function testCommissionCalculation($expected, $binData, $currencyData, array $transactionData)
    {
        $binLookupStub = $this->createMock(BinLookup::class);
        $binLookupStub->method('getCountryNameByBin')->willReturn($binData);
        $currencyStub = $this->createMock(CurrencyProvider::class);
        $currencyStub->method('getCurrencyRate')->willReturn($currencyData);
        $transaction = new TransactionInfo(...array_values($transactionData));

        $calculator = new SimpleCommissionCalculator($binLookupStub, $currencyStub);

        $this->assertEquals(
            $expected,
            $calculator->calculate($transaction)
        );
    }
}