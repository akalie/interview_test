#!/usr/bin/php
<?php declare(strict_types=1);

include './vendor/autoload.php';

use blabla\BinLookup\BinlistBinLookup;
use blabla\CommissionCalculator\SimpleCommissionCalculator;
use blabla\CurrencyProvider\ExchangeratesapiCurrencyProvider;
use blabla\SimpleFileTransactionInfoExtractor;
use GuzzleHttp\Client;

$transactionsFile = $argv[1];

$client = new Client();
$binLookup = new BinlistBinLookup($client);
$currencyProvider = new ExchangeratesapiCurrencyProvider($client);
$calculator = new SimpleCommissionCalculator($binLookup, $currencyProvider);

$iterator = new SimpleFileTransactionInfoExtractor($transactionsFile);
while ($transaction = $iterator->next()) {
    echo $calculator->calculate($transaction) . ' (' . $calculator->calculateWithCeil($transaction) . ')' . PHP_EOL;
}
