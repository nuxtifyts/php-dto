<?php

namespace Nuxtifyts\PhpDto\Attributes\Class;

use Attribute;
use InvalidArgumentException;
use Nuxtifyts\PhpDto\Normalizers\Normalizer;
use Nuxtifyts\PhpDto\Support\Arr;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class WithNormalizer
{
    /** @var array<array-key, class-string<Normalizer>> */
    public array $classStrings;

    /**
     * @param class-string<Normalizer> $classString
     * @param class-string<Normalizer> ...$classStrings
     */
    public function __construct(string $classString, string ...$classStrings)
    {
        $arrOfClassStrings = [$classString, ...$classStrings];

        if (!Arr::isArrayOfClassStrings($arrOfClassStrings, Normalizer::class)) {
            throw new InvalidArgumentException('expects a list of class strings of normalizers');
        }

        $this->classStrings = $arrOfClassStrings;
    }
}
