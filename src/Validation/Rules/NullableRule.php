<?php

namespace Nuxtifyts\PhpDto\Validation\Rules;

class NullableRule implements ValidationRule
{
    public string $name {
        get {
            return 'nullable';
        }
    }

    public function evaluate(mixed $value): bool
    {
        return true;
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
        return '';
    }
}
