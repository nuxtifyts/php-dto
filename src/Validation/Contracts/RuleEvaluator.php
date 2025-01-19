<?php

namespace Nuxtifyts\PhpDto\Validation\Contracts;

interface RuleEvaluator
{
    public function evaluate(mixed $value): bool;
}
