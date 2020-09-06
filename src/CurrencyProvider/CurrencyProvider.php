<?php declare(strict_types=1);


namespace blabla\CurrencyProvider;


interface CurrencyProvider
{
    public function getCurrencyRate(string $currencyName): float;
}