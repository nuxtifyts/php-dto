<?php

namespace Nuxtifyts\PhpDto\Validation\Rules;

use Nuxtifyts\PhpDto\Exceptions\ValidationRuleException;

interface ValidationRule
{
    public string $name { get; }

    public function evaluate(mixed $value): bool;

    /** 
     * @param ?array<string, mixed> $parameters
     * 
     * @throws ValidationRuleException
     */
    public static function make(?array $parameters = null): self;

    public function validationMessage(): string;
}
