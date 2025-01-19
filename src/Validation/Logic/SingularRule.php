<?php

namespace Nuxtifyts\PhpDto\Validation\Logic;

use Nuxtifyts\PhpDto\Exceptions\LogicalRuleException;
use Nuxtifyts\PhpDto\Validation\Contracts\RuleEvaluator;
use Override;

class SingularRule extends LogicalRule
{
    /**
     * @throws LogicalRuleException
     */
    #[Override]
    public function addRule(RuleEvaluator $rule): static
    {
        if ($this->rules->isNotEmpty()) {
            throw LogicalRuleException::unableToCreateRule('SingularRule can only have one rule');
        }

        $this->rules->push($rule);
        return $this;
    }

    public function evaluate(mixed $value): bool
    {
        return (bool) $this->rules->first()?->evaluate($value);
    }
}
