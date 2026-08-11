<?php

use App\Rules\ValidCpfOrCnpj;

it('accepts empty or null values as valid', function () {
    $rule = new ValidCpfOrCnpj();

    $failures = [];
    $fail = function (string $message) use (&$failures) {
        $failures[] = $message;
        return null;
    };

    $rule->validate('document', null, $fail);
    $rule->validate('document', '', $fail);

    expect($failures)->toBeEmpty();
});

it('accepts valid CPF values', function () {
    $rule = new ValidCpfOrCnpj();

    $failures = [];
    $fail = function (string $message) use (&$failures) {
        $failures[] = $message;
        return null;
    };

    $rule->validate('document', '529.982.247-25', $fail);

    expect($failures)->toBeEmpty();
});

it('accepts valid CNPJ values', function () {
    $rule = new ValidCpfOrCnpj();

    $failures = [];
    $fail = function (string $message) use (&$failures) {
        $failures[] = $message;
        return null;
    };

    $rule->validate('document', '45.723.174/0001-10', $fail);

    expect($failures)->toBeEmpty();
});

it('rejects invalid CPF or CNPJ values', function () {
    $rule = new ValidCpfOrCnpj('Invalid CPF or CNPJ.');

    $failures = [];
    $fail = function (string $message) use (&$failures) {
        $failures[] = $message;
        return null;
    };

    $rule->validate('document', '111.111.111-11', $fail);
    $rule->validate('document', '12345678901', $fail);
    $rule->validate('document', '12.345.678/0001-00', $fail);

    expect($failures)->not()->toBeEmpty();

    expect($failures)->toBe([
        'Invalid CPF or CNPJ.',
        'Invalid CPF or CNPJ.',
        'Invalid CPF or CNPJ.',
    ]);
});
