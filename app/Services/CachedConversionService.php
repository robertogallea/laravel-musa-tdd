<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CachedConversionService implements ConversionServiceInterface
{
    public function __construct(protected ConversionServiceInterface $innerService)
    {

    }

    public function convert(string $from, string $to, float $amount): ?float
    {
        return Cache::remember(
            "conversion_from_{$from}_to_{$to}",
            60,
            fn() => $this->innerService->convert($from, $to, $amount)
        );
    }
}
