<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidPhoneNumber implements ValidationRule
{
    public function __construct(
        private readonly int $minDigits = 10,
        private readonly int $maxDigits = 13,
        private readonly string $message = 'Invalid phone number.'
    ) {}

    /**
     * @param  Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }

        $digits = preg_replace('/\D+/', '', (string) $value) ?? '';

        if ($digits === '') {
            return;
        }

        $len = strlen($digits);
        if ($len < $this->minDigits || $len > $this->maxDigits) {
            $fail($this->message);
        }
    }
}
