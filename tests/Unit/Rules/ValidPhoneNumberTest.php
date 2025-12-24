<?php

use App\Rules\ValidPhoneNumber;

it('accepts empty or null values as valid', function () {
    $rule = new ValidPhoneNumber();

    $failures = [];
    $fail = function (string $message) use (&$failures) {
        $failures[] = $message;
        return null;
    };

    $rule->validate('phone', null, $fail);
    $rule->validate('phone', '', $fail);

    expect($failures)->toBeEmpty();
});

it('accepts values within the default digit range', function () {
    $rule = new ValidPhoneNumber();

    $failures = [];
    $fail = function (string $message) use (&$failures) {
        $failures[] = $message;
        return null;
    };

    $rule->validate('phone', '+55 (11) 91234-5678', $fail);
    $rule->validate('phone', '1191234567', $fail);

    expect($failures)->toBeEmpty();
});

it('rejects values shorter than the minimum or longer than the maximum', function () {
    $rule = new ValidPhoneNumber();

    $failures = [];
    $fail = function (string $message) use (&$failures) {
        $failures[] = $message;
        return null;
    };

    $rule->validate('phone', '123456789', $fail);
    $rule->validate('phone', '+55 111 2345 67890', $fail);

    expect($failures)->toBe([
        'Invalid phone number.',
        'Invalid phone number.',
    ]);
});
