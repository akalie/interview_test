<?php declare(strict_types=1);


namespace blabla\CommissionCalculator;


use blabla\TransactionInfo;

interface CommissionCalculator
{
    public function calculate(TransactionInfo $transaction): float;
}