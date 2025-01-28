<?php

namespace Nuxtifyts\PhpDto\Support\Validation\Contracts;

use Nuxtifyts\PhpDto\Contexts\ClassContext;
use Nuxtifyts\PhpDto\Data;

interface RuleReferer
{
    /**
     * @template T of Data
     *
     * @param ClassContext<T> $classContext
     *
     * @return list<string>|array<string, mixed>
     */
    public static function getRulesFromClassContext(ClassContext $classContext): array;
}
