<?php

namespace Nuxtifyts\PhpDto\Validation\Contracts;

use Nuxtifyts\PhpDto\Exceptions\ValidationRuleException;
use Nuxtifyts\PhpDto\Support\Collection;

interface RuleGroup
{
    /** @var Collection<array-key, RuleEvaluator> */
    public Collection $rules { get; }

    /**
     * @throws ValidationRuleException
     */
    public function addRule(RuleEvaluator $rule): static;
}
