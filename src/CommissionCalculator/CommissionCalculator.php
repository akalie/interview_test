<?php declare(strict_types=1);


namespace blabla\CommissionCalculator;


interface CommissionCalculator
{
    public function calculate(float $amount, string $countryName): float;
}