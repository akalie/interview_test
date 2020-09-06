#!/usr/bin/php
<?php declare(strict_types=1);

include './vendor/autoload.php';


use blabla\BinLookup\BinlistBinLookup;
use blabla\CommissionCalculator\SimpleCommissionCalculator;
use blabla\CurrencyConverter\SimpleToEuroCurrencyConverter;
use blabla\CurrencyProvider\ExchangeratesapiCurrencyProvider;
use blabla\SimpleFileTransactionInfoExtractor;
use GuzzleHttp\Client;
use Psr\Log\NullLogger;

$transactionsFile = $argv[1];

$client = new Client();
$binLookup = new BinlistBinLookup($client);
$currencyProvider = new ExchangeratesapiCurrencyProvider($client);
$calculator = new SimpleCommissionCalculator(0.01, 0.02);
$iterator = new SimpleFileTransactionInfoExtractor($transactionsFile);
$converter = new SimpleToEuroCurrencyConverter($currencyProvider);

$logger = new class extends NullLogger {
    public function log($level, $message, array $context = array())
    {
        echo 'Failed to calculate commission with error "' . $message . ', move to the next transaction.' . PHP_EOL;
    }
};


while ($transaction = $iterator->next()) {
    try {
        $countryName = $binLookup->getCountryNameByBin($transaction->bin);
    } catch (Throwable $e) {
        $logger->alert($e->getMessage());

        continue;
    }

    try {
        $convertedToEuroAmount = $converter->convert(
            $transaction->amount, $transaction->getCurrency()
        );
    } catch (Throwable $e) {
        $logger->alert($e->getMessage());
        continue;
    }


    echo $calculator->calculate($convertedToEuroAmount, $countryName)
        . ' (' . $calculator->calculateWithCeil($convertedToEuroAmount, $countryName) . ')'
        . PHP_EOL;
}
