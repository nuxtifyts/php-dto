<?php

namespace Nuxtifyts\PhpDto\Support\Validation;

use Nuxtifyts\PhpDto\Support\Validation\Contracts\DataValidator;

final readonly class Validator implements DataValidator
{
    /**
     * @param array<array-key, mixed> $value
     *
     * @return array<array-key, mixed>
     *
     */
    public static function validate(array $value): array
    {
        return $value; // Validator should be replaced from config.
    }
}
