<?php

namespace Nuxtifyts\PhpDto\Tests\Dummies;

use Nuxtifyts\PhpDto\Attributes\Class\MapName;
use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Enums\LetterCase;

#[MapName(from: [ LetterCase::SNAKE, LetterCase::KEBAB, LetterCase::PASCAL ])]
final readonly class PropertyNameMapperData extends Data
{
    public function __construct(
        public string $camelCase
    ) {
    }
}
