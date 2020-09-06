<?php


namespace blabla\CommissionCalculator;


class SimpleCommissionCalculator implements CommissionCalculator
{
    public float $europeCommissionMultiplier;
    public float $nonEuropeCommissionMultiplier;

    protected static array $europeCountries
        = [
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

    public function __construct(float $europeCommissionMultiplier,
        float $nonEuropeCommissionMultiplier
    ) {
        $this->europeCommissionMultiplier = $europeCommissionMultiplier;
        $this->nonEuropeCommissionMultiplier = $nonEuropeCommissionMultiplier;
    }

    public function calculate(float $amount, string $countryName): float
    {
        $commissionMultiplier = $this->getCommissionMultiplier($countryName);

        return $amount * $commissionMultiplier;
    }

    public function calculateWithCeil(float $amount, string $countryName): float
    {
        $commission = $this->calculate($amount, $countryName);

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
            ? $this->europeCommissionMultiplier
            : $this->nonEuropeCommissionMultiplier;
    }
}