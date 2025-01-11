<?php

namespace Nuxtifyts\PhpDto\Validation\Rules\Concerns;

use Nuxtifyts\PhpDto\Support\Arr;
use Nuxtifyts\PhpDto\Exceptions\ValidationRuleException;

trait MinMaxValues
{
    /** 
     *  @param ?array<string, mixed> $parameters
     *  @return array{ 0: int|null, 1: int|null }
     * 
     *  @throws ValidationRuleException
     */
    protected static function getMinMaxValues(
        ?array $parameters = null,
        string $minKey = 'min',
        string $maxKey = 'max'
    ): array {
        if (
            (!is_null($min = Arr::getIntegerOrNull($parameters ?? [], $minKey))
                && $min < 0)
            || (!is_null($max = Arr::getIntegerOrNull($parameters ?? [], $maxKey))
                && $max < $min)
        ) {
            throw ValidationRuleException::invalidParameters();
        }

        return [$min, $max];
    }
}
