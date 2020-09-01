<?php declare(strict_types=1);


namespace blabla;


class TransactionInfo
{
    public int $bin;
    public float $amount;
    protected string $currency;

    public function __construct(int $bin, float $amount, string $currency)
    {
        $this->bin = $bin;
        $this->amount = $amount;
        $this->currency = trim($currency);
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = trim($currency);
    }
}