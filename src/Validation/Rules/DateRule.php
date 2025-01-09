<?php

namespace Nuxtifyts\PhpDto\Validation\Rules;

class DateRule implements ValidationRule
{
    public string $name {
        get {
            return 'date';
        }
    }

    public function evaluate(mixed $value): bool
    {
        return is_string($value) && strtotime($value) !== false;
    }

    /** 
     *  @param ?array<string, mixed> $parameters
     */
    public static function make(?array $parameters = null): self
    {
        return new self();
    }

    public function validationMessage(): string
    {
        return 'The :attribute must be a valid date.';
    }
}
