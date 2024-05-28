<?php

namespace App\Services;

interface ConversionServiceInterface
{
    public function convert(string $from, string $to, float $amount): ?float;
}
