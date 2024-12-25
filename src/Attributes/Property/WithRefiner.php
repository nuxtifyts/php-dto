<?php

namespace Nuxtifyts\PhpDto\Attributes\Property;

use Attribute;
use Nuxtifyts\PhpDto\DataRefiners\DataRefiner;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class WithRefiner
{
    /** @var array<array-key, mixed> */
    private array $refinerArgs;

    /**
     * @param class-string<DataRefiner> $refinerClass
     */
    public function __construct(
        private readonly string $refinerClass,
        mixed ...$refinerArgs
    ) {
        $this->refinerArgs = $refinerArgs;
    }

    public function getRefiner(): DataRefiner
    {
        return new $this->refinerClass(...$this->refinerArgs);
    }
}
