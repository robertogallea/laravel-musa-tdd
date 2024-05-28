<?php

use App\Services\CachedConversionService;
use App\Services\ConversionServiceInterface;

it('caches conversion information', function () {
    $conversionService = Mockery::mock(
        ConversionServiceInterface::class,
        function ($mock) {
            $mock->shouldReceive('convert')
                ->with('EUR', 'USD', 1)
                ->once()
                ->andReturn(1.1);
        }
    );
    $service = new CachedConversionService($conversionService);
    $service->convert('EUR', 'USD', 1);
    $service->convert('EUR', 'USD', 1);
});


test('the cache lasts for 1 minute', function () {
    $conversionService = Mockery::mock(
        ConversionServiceInterface::class,
        function ($mock) {
            $mock->shouldReceive('convert')
                ->with('EUR', 'USD', 1)
                ->once()
                ->andReturn(1.1);
        }
    );
    $service = new CachedConversionService($conversionService);
    $service->convert('EUR', 'USD', 1);
    $this->travelTo(now()->addSeconds(59));
    $service->convert('EUR', 'USD', 1);
});

test('the cache expires after 1 minute', function () {
    $conversionService = Mockery::mock(
        ConversionServiceInterface::class,
        function ($mock) {
            $mock->shouldReceive('convert')
                ->with('EUR', 'USD', 1)
                ->twice()
                ->andReturn(1.1);
        }
    );
    $service = new CachedConversionService($conversionService);
    $service->convert('EUR', 'USD', 1);
    $this->travelTo(now()->addMinute());
    $service->convert('EUR', 'USD', 1);
});

