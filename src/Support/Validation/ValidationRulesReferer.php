<?php

namespace Nuxtifyts\PhpDto\Support\Validation;

use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Support\Validation\Contracts\RuleReferer;
use Nuxtifyts\PhpDto\Contexts\ClassContext;

final readonly class ValidationRulesReferer implements RuleReferer
{
    public static function createInstance(mixed ...$args): self
    {
        return new self();
    }

    /**
     * @template T of Data
     *
     * @param ClassContext<T> $classContext
     *
     * @return list<string>|array<string, mixed>
     */
    public static function getRulesFromClassContext(ClassContext $classContext): array
    {
        return []; // Rule referer should be replaced from config.
    }
}
