<?php

namespace Nuxtifyts\PhpDto\Validation\Logic;

use Nuxtifyts\PhpDto\Validation\Contracts\RuleEvaluator;

class OrRule extends LogicalRule
{
    public function evaluate(mixed $value): bool
    {
        return $this->rules->some(
            static fn (RuleEvaluator $rule) => $rule->evaluate($value)
        );
    }

    public function validationMessageTree(): array
    {
        return [
            'or' => $this->rules
                ->map(self::resolveValidationMessages(...))
                ->collapse(preserveKeys: true)
                ->all()
        ];
    }
}
