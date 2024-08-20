<?php

namespace App\Action\CheckSum;

class CheckSum
{

    public static function createChecksum($concatenatedString, $exchangeKey)
    {
        // Combine the concatenated string with the exchange key as salt
        $saltedString = $concatenatedString . $exchangeKey;

        // Generate the checksum using sha512
        $checksum = hash('sha512', $saltedString);

        return $checksum;
    }

    public static function isChecksumValid($checksum1, $checksum2)
    {
        return hash_equals($checksum1, $checksum2);
    }
}
