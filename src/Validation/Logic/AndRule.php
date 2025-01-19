<?php

namespace Nuxtifyts\PhpDto\Validation\Logic;

use Nuxtifyts\PhpDto\Validation\Contracts\RuleEvaluator;

class AndRule extends LogicalRule
{
    public function evaluate(mixed $value): bool
    {
        return $this->rules->every(
            static fn (RuleEvaluator $rule) => $rule->evaluate($value)
        );
    }
}
