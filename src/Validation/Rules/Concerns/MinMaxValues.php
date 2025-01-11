<?php

namespace Nuxtifyts\PhpDto\Validation\Rules\Concerns;

use Nuxtifyts\PhpDto\Support\Arr;
use Nuxtifyts\PhpDto\Exceptions\ValidationRuleException;
use Nuxtifyts\PhpDto\Enums\Property\Type;

trait MinMaxValues
{
    /** 
     * @param ?array<string, mixed> $parameters
     * @param Type::INT | Type::FLOAT $type
     * 
     * @return array{ 0: int|null|float, 1: int|null|float }
     * 
     * @throws ValidationRuleException
     */
    protected static function getMinMaxValues(
        ?array $parameters = null,
        string $minKey = 'min',
        string $maxKey = 'max',
        Type $type = Type::INT
    ): array {
        $arrFunc = match ($type) {
            Type::INT => 'getIntegerOrNull',
            Type::FLOAT => 'getFloatOrNull',
        };

        $min = match ($type) {
            Type::INT => Arr::getIntegerOrNull($parameters ?? [], $minKey),
            Type::FLOAT => Arr::getFloatOrNull($parameters ?? [], $minKey),
        };

        $max = match ($type) {
            Type::INT => Arr::getIntegerOrNull($parameters ?? [], $maxKey),
            Type::FLOAT => Arr::getFloatOrNull($parameters ?? [], $maxKey),
        };

        if (
            (!is_null($min) && $min < 0)
            || (!is_null($max) && ($max <= 0 || $min > $max))
        ) {
            throw ValidationRuleException::invalidParameters();
        }

        return [$min, $max];
    }
}
