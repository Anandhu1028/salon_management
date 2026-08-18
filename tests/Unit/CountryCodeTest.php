<?php

use App\Models\CountryCode;

test('it can return the default dial code', function () {
    $code = CountryCode::getDefaultCode();

    expect($code)->toBeString()
        ->and($code)->not->toBeEmpty();
});
