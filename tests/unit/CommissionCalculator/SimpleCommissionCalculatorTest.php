<?php


namespace unit\CurrencyProvider;


use blabla\BinLookup\BinLookup;
use blabla\CommissionCalculator\SimpleCommissionCalculator;
use blabla\CurrencyProvider\CurrencyProvider;
use blabla\TransactionInfo;
use PHPUnit\Framework\TestCase;

class SimpleCommissionCalculatorTest extends TestCase
{

    const EUROPE_COMMISSION_RATE_DUMMY = 0.01;
    const NON_EUROPE_COMMISSION_RATE_DUMMY = 0.02;

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
            self::EUROPE_COMMISSION_RATE_DUMMY,
            self::NON_EUROPE_COMMISSION_RATE_DUMMY
        );

        $this->assertEquals($result, $calculator->isEuropeCountry($country));
    }

    public function commissionMultiplierCalculatingProvider(): array
    {
        return [
            ['BG', self::EUROPE_COMMISSION_RATE_DUMMY],
            ['BY', self::NON_EUROPE_COMMISSION_RATE_DUMMY],
        ];
    }

    /**
     * @dataProvider commissionMultiplierCalculatingProvider
     */
    public function testCommissionMultiplierCalculating(string $country, float $result)
    {
        $calculator = new SimpleCommissionCalculator(
            self::EUROPE_COMMISSION_RATE_DUMMY,
            self::NON_EUROPE_COMMISSION_RATE_DUMMY
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
            self::EUROPE_COMMISSION_RATE_DUMMY,
            self::NON_EUROPE_COMMISSION_RATE_DUMMY
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
                'amount' => 100.00,
                'countryName' => 'DK',
            ],
            [
                'expectedResult' => 0.41876046901173,
                'amount' => 41.8760469012,
                'countryName' => 'LT',
            ],
            [
                'expectedResult' => 1.5814027041986,
                'amount' => 79.0701352099,
                'countryName' => 'JP',
            ],
            [
                'expectedResult' => 2.177554438861,
                'amount' => 108.877721943,
                'countryName' => 'US',
            ],
            [
                'expectedResult' => 44.640366051002,
                'amount' => 2232.01830255,
                'countryName' => 'GB',
            ],
        ];
    }

    /**
     * @dataProvider commissionCalculationProvider
     */
    public function testCommissionCalculation($expected, $amount, $countryName)
    {
        $calculator = new SimpleCommissionCalculator(
            self::EUROPE_COMMISSION_RATE_DUMMY,
            self::NON_EUROPE_COMMISSION_RATE_DUMMY
        );


        $this->assertEquals(
            $expected,
            $calculator->calculate($amount, $countryName)
        );
    }
}