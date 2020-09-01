<?php


namespace blabla\CommissionCalculator;


use blabla\BinLookup\BinLookup;
use blabla\CurrencyProvider\CurrencyProvider;
use blabla\TransactionInfo;

class SimpleCommissionCalculator implements CommissionCalculator
{
    protected const DEFAULT_CURRENCY = 'EUR';

    public const EUROPE_COMMISSION_MULTIPLIER = 0.01;
    public const NON_EUROPE_COMMISSION_MULTIPLIER = 0.02;

    protected static array $europeCountries = [
        'AT' => true,
        'BE' => true,
        'BG' => true,
        'CY' => true,
        'CZ' => true,
        'DE' => true,
        'DK' => true,
        'EE' => true,
        'ES' => true,
        'FI' => true,
        'FR' => true,
        'GR' => true,
        'HR' => true,
        'HU' => true,
        'IE' => true,
        'IT' => true,
        'LT' => true,
        'LU' => true,
        'LV' => true,
        'MT' => true,
        'NL' => true,
        'PO' => true,
        'PT' => true,
        'RO' => true,
        'SE' => true,
        'SI' => true,
        'SK' => true,
    ];

    protected BinLookup $binLookup;
    protected CurrencyProvider $currencyProvider;

    public function __construct(BinLookup $binLookup, CurrencyProvider $currencyProvider) {
        $this->binLookup = $binLookup;
        $this->currencyProvider = $currencyProvider;
    }

    public function calculate(TransactionInfo $transaction): float
    {
        $country = $this->binLookup->getCountryNameByBin($transaction->bin);
        $rateToEuro = static::DEFAULT_CURRENCY === $transaction->getCurrency()
            ? 1.
            : $this->currencyProvider->getCurrencyRate(
                $transaction->getCurrency()
            );

        $commissionMultiplier = $this->getCommissionMultiplier($country);

        return ($transaction->amount / $rateToEuro) * $commissionMultiplier;
    }

    public function calculateWithCeil(TransactionInfo $transaction): float
    {
        $commission = $this->calculate($transaction);

        return $this->ceil($commission);
    }

    function ceil(float $value, int $precision = 2): float
    {
        $offset = 0.5;
        if ($precision !== 0) {
            $offset /= 10 ** $precision;
        }
        return round($value + $offset, $precision, PHP_ROUND_HALF_DOWN);
    }

    public function isEuropeCountry(string $country): bool
    {
        return isset(static::$europeCountries[$country]);
    }

    public function getCommissionMultiplier(string $country): float
    {
        return $this->isEuropeCountry($country)
            ? static::EUROPE_COMMISSION_MULTIPLIER
            : static::NON_EUROPE_COMMISSION_MULTIPLIER;
    }
}