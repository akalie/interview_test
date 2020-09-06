<?php declare(strict_types=1);


namespace blabla\CurrencyConverter;


use blabla\CurrencyProvider\CurrencyProvider;

class SimpleToEuroCurrencyConverter implements CurrencyConverter
{
    const EURO_CURRENCY_NAME = 'EUR';

    protected CurrencyProvider $currencyProvider;

    public function __construct(CurrencyProvider $currencyProvider)
    {
        $this->currencyProvider = $currencyProvider;
    }

    /**
     * Converts one currency to another
     *
     * @param float  $amount       sum you want to exchange
     * @param string $currencyFrom given sum currency name
     * @param string $currencyTo   currency convert to name
     *
     * @return float amount in currency convert to name
     * @throws CurrencyConverterException
     */
    public function convert(
        float $amount,
        string $currencyFrom,
        string $currencyTo = self::EURO_CURRENCY_NAME
    ): float {
        if (self::EURO_CURRENCY_NAME !== $currencyTo) {
            throw new CurrencyConverterException(
                '"' . self::EURO_CURRENCY_NAME
                . '" is the only currencyTo supported by SimpleToEuroCurrencyConverter'
            );
        }

        if (self::EURO_CURRENCY_NAME === $currencyFrom) {
            return $amount;
        }

        $rateToEuro = $this->currencyProvider->getCurrencyRate($currencyFrom);

        return $amount / $rateToEuro;
    }
}