<?php declare(strict_types=1);


namespace blabla\BinLookup;


interface BinLookup
{
    /** Bank Identification Number */
    public function getCountryNameByBin(int $bin): string;
}