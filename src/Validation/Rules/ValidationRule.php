<?php

namespace Nuxtifyts\PhpDto\Validation\Rules;

use Nuxtifyts\PhpDto\Exceptions\ValidationRuleException;
use Nuxtifyts\PhpDto\Validation\Contracts\RuleEvaluator;

interface ValidationRule extends RuleEvaluator
{
    public string $name { get; }

    /** 
     * @param ?array<string, mixed> $parameters
     * 
     * @throws ValidationRuleException
     */
    public static function make(?array $parameters = null): self;

    public function validationMessage(): string;
}
