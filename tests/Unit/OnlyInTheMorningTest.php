<?php

it('blocks requests in A.M. hours', function () {
    $this->travelTo(today()->setHour(11)->setMinute(59));

    $middleware = new \App\Http\Middleware\OnlyInTheMorning();
    $request = new Illuminate\Http\Request();

    $middleware->handle($request, function() {
        // se sono arrivato qui, il middleware non sta funzionando correttamente
        $this->assertTrue(false); // fallimento esplicito del test
    });

    $this->assertTrue(true);
});
