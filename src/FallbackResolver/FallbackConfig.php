<?php

namespace Nuxtifyts\PhpDto\FallbackResolver;

use BackedEnum;

readonly class FallbackConfig
{
    /**
     * @param BackedEnum|array<array-key, mixed>|int|string|float|bool|null $value
     * @param ?class-string<FallbackResolver> $resolverClass
     */
    public function __construct(
        public BackedEnum|array|int|string|float|bool|null $value,
        public ?string $resolverClass = null
    ) {
    }
}
