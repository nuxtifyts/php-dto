<?php

namespace Nuxtifyts\PhpDto\Validation\Logic;

use Nuxtifyts\PhpDto\Support\Collection;
use Nuxtifyts\PhpDto\Validation\Contracts\RuleEvaluator;
use Nuxtifyts\PhpDto\Validation\Contracts\RuleGroup;

abstract class LogicalRule implements RuleGroup, RuleEvaluator
{
    /** @var Collection<array-key, RuleEvaluator> */
    protected Collection $_rules;

    /** @var Collection<array-key, RuleEvaluator> */
    public Collection $rules {
        get {
            return $this->_rules;
        }
    }

    public function __construct()
    {
        $this->_rules = new Collection();
    }

    public function addRule(RuleEvaluator $rule): static
    {
        $this->rules->push($rule);
        return $this;
    }
}
