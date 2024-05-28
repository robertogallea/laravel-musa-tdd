<?php

use App\Mail\NotExistincyCurrencyMail;
use App\Models\Conversion;
use App\Services\ConversionServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

it('can convert an amount between two currencies', function ($from, $to, $amount, $rate, $result) {
    // Set di precondizioni

    $this->mock(
        ConversionServiceInterface::class,
        function ($mock) use ($result, $amount, $to, $from) {
            $mock->shouldReceive('convert')
                ->with($from, $to, $amount)
                ->andReturn($result);
        }
    );

    // Set di operazioni da svolgere
    $response = postJson('/api/convert', [
        'from' => $from,
        'to' => $to,
        'amount' => $amount,
    ]);

    // Set di asserzioni da verificare
    $response->assertOk()
        ->assertJson([
            'result' => $result,
        ]);
})->with([
    ['EUR', 'EUR', 1, 1.0, 1.0],
    ['EUR', 'USD', 1, 1.1, 1.1],
    ['EUR', 'USD', 2, 1.1, 2.2],
]);


it('can convert an amount between two currencies with flipped currencies', function ($from, $to, $amount, $rate, $result) {
    // Set di precondizioni

    $this->mock(
        ConversionServiceInterface::class,
        function ($mock) use ($result, $amount, $to, $from) {
            $mock->shouldReceive('convert')
                ->with($to, $from, $amount)
                ->andReturn(round($result, 2));
        }
    );
    // Set di operazioni da svolgere
    $response = postJson('/api/convert', [
        'from' => $to,
        'to' => $from,
        'amount' => $amount,
    ]);

    // Set di asserzioni da verificare
    $response->assertOk()
        ->assertJson([
            'result' => round($result, 2),
        ]);
})->with([
    ['EUR', 'EUR', 1, 1.0, 1 / 1.0],
    ['EUR', 'USD', 1, 1.1, 1 / 1.1],
    ['EUR', 'USD', 2, 1.1, 2 / 1.1],
]);


it('validates data', function ($notValidKey, $notValidValue) {
    // set di dati validi
    $validData = [
        'from' => 'EUR',
        'to' => 'EUR',
        'amount' => 1.0,
    ];

    postJson('/api/convert', [
        ...$validData,
        $notValidKey => $notValidValue,
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrorFor($notValidKey);
})->with([
    ['from', null],
    ['from', 'XX'],
    ['from', 'XXXX'],
    ['from', 1],
    ['from', true],
    ['to', null],
    ['to', '??'],
    ['to', '????'],
    ['to', 1],
    ['to', true],
    ['amount', null],
    ['amount', 'aaa'],
    ['amount', true],
    ['amount', -1],
]);

it('return an error message if currency does not exist', function () {
    Mail::fake();

    $this->withoutExceptionHandling();
    $notExistingConversion = [
        'from' => 'XXX',
        'to' => 'YYY',
        'amount' => 1.0,
    ];

    $this->mock(
        ConversionServiceInterface::class,
        function ($mock) {
            $mock->shouldReceive('convert')
                ->with('XXX', 'YYY', 1.0)
                ->andReturn(null);
        }
    );

    postJson('/api/convert', $notExistingConversion)
        ->assertNotFound()
        ->assertJson(['message' => 'Conversion rate for currencies XXX and YYY does not exist']);
});

it('sends an email to the administrator if a not existing currency conversion is requested', function () {
    Mail::fake();

    $notExistingConversion = [
        'from' => 'XXX',
        'to' => 'YYY',
        'amount' => 1.0,
    ];

    $this->mock(
        ConversionServiceInterface::class,
        function ($mock) {
            $mock->shouldReceive('convert')
                ->with('XXX', 'YYY', 1.0)
                ->andReturn(null);
        }
    );

    postJson('/api/convert', $notExistingConversion);

    Mail::assertSent(NotExistincyCurrencyMail::class, function ($mail) {
        return ($mail->fromCurrency == 'XXX') &&
            ($mail->toCurrency == 'YYY') &&
            ($mail->to[0]['address'] == 'admin@admin.it');
    });
});


it('blocks requests in A.M. hours', function () {
    $this->travelTo(today()->setHour(11)->setMinute(59));

    postJson('/api/convert', [])
        ->assertForbidden()
        ->assertJson(['message' => 'The service can be used only in the morning']);
    ;
});
