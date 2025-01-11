<?php

namespace Nuxtifyts\PhpDto\Validation\Rules;

use Nuxtifyts\PhpDto\Validation\Rules\ValidationRule;
use Override;

class EmailRule extends RegexRule
{
    public string $name {
        get {
            return 'email';
        }
    }

    /**
     * @param ?array<string, mixed> $parameters
     */
    #[Override]
    public static function make(?array $parameters = null): self
    {
        $instance = new self();

        $instance->pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';

        return $instance;
    }

    #[Override]
    public function validationMessage(): string
    {
        return 'The :attribute field must be a valid email address.';
    }
}
