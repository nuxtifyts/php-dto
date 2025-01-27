<?php

namespace Nuxtifyts\PhpDto\Validation\Logic;

use Nuxtifyts\PhpDto\Exceptions\LogicalRuleException;
use Nuxtifyts\PhpDto\Validation\Contracts\RuleEvaluator;
use Override;

class TruthyRule extends LogicalRule
{
    /** 
     * @throws LogicalRuleException 
     */
    #[Override]
    public function addRule(RuleEvaluator $rule): never
    {
        throw LogicalRuleException::unableToCreateRule('TruthyRule cannot have nested rules');
    }
    
    public function evaluate(mixed $value): bool
    {
        return true;
    }
    
    public function validationMessageTree(): array
    {
        return [];
    }
}
