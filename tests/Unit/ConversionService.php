<?php

use App\Models\Conversion;
use App\Services\EloquentConversionService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can convert between two currencies', function() {
    $from = 'EUR';
    $to = 'USD';
    $rate = 1.1;
    $amount = 1;

    Conversion::factory()->create([
        'from' => $from,
        'to' => $to,
        'rate' => $rate * 100,
    ]);

    $service = new EloquentConversionService();

    $result = $service->convert($from, $to, $amount);

    // PHPUnit-flavoured
    $this->assertEquals($result, $amount * $rate);

    // Pest-flavoured
    expect($result)->toBe($amount * $rate);
});
