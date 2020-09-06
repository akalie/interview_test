<?php declare(strict_types=1);


namespace blabla\CurrencyConverter;


interface CurrencyConverter
{
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
    public function convert(float $amount, string $currencyFrom,
        string $currencyTo
    ): float;
}