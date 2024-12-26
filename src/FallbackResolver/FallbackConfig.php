<?php

namespace Nuxtifyts\PhpDto\FallbackResolver;

use BackedEnum;

readonly class FallbackConfig
{
    /**
     * @param ?class-string<FallbackResolver> $resolverClass
     */
    public function __construct(
        public BackedEnum|int|string|float|bool|null $value,
        public ?string $resolverClass = null
    ) {
    }
}
