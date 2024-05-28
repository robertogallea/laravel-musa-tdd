<?php

namespace App\Services;

use App\Models\Conversion;

class EloquentConversionService implements ConversionServiceInterface
{

    public function convert(string $from, string $to, float $amount): ?float
    {
        $conversion = Conversion::where('from', $from)
            ->where('to', $to)->first();

        if ($conversion) {
            return $amount * $conversion->rate / 100;
        }

        $conversion = Conversion::where('from', $to)
            ->where('to', $from)->first();

        if ($conversion) {
            return round($amount / $conversion->rate * 100, 2);
        }

        return null;
    }
}
