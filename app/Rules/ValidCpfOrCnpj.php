<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidCpfOrCnpj implements ValidationRule
{
    public function __construct(private readonly string $message = 'Invalid CPF or CNPJ.') {}

    /**
     * @param  Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $doc = preg_replace('/\D+/', '', (string) $value) ?? '';

        if ($doc === '') {
            return;
        }

        if ($this->isValidCpf($doc) || $this->isValidCnpj($doc)) {
            return;
        }

        $fail($this->message);
    }

    private function isValidCpf(string $cpf): bool
    {
        if (strlen($cpf) !== 11) {
            return false;
        }

        if (preg_match('/^(\d)\1+$/', $cpf)) {
            return false;
        }

        $digits = array_map('intval', str_split($cpf));

        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += $digits[$i] * (10 - $i);
        }
        $rest = ($sum * 10) % 11;
        $rest = ($rest === 10) ? 0 : $rest;
        if ($rest !== $digits[9]) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += $digits[$i] * (11 - $i);
        }
        $rest = ($sum * 10) % 11;
        $rest = ($rest === 10) ? 0 : $rest;

        return $rest === $digits[10];
    }

    private function isValidCnpj(string $cnpj): bool
    {
        if (strlen($cnpj) !== 14) {
            return false;
        }

        if (preg_match('/^(\d)\1+$/', $cnpj)) {
            return false;
        }

        $digits = array_map('intval', str_split($cnpj));

        $weights1 = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $weights2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += $digits[$i] * $weights1[$i];
        }
        $rest = $sum % 11;
        $check1 = ($rest < 2) ? 0 : (11 - $rest);
        if ($check1 !== $digits[12]) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 13; $i++) {
            $sum += $digits[$i] * $weights2[$i];
        }
        $rest = $sum % 11;
        $check2 = ($rest < 2) ? 0 : (11 - $rest);

        return $check2 === $digits[13];
    }
}
