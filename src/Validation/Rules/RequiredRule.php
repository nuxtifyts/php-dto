<?php

namespace Nuxtifyts\PhpDto\Validation\Rules;

class RequiredRule implements ValidationRule
{
    public string $name {
        get {
            return 'required';
        }
    }

    public function evaluate(mixed $value): bool
    {
        return !empty($value);
    }

    /** 
     * @param ?array<string, mixed> $parameters
     */
    public static function make(?array $parameters = null): self
    {
        return new self();
    }

    public function validationMessage(): string
    {
        return 'The :attribute field is required.';
    }
}
