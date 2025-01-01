<?php

namespace Nuxtifyts\PhpDto\Attributes\Class;

use Attribute;
use Nuxtifyts\PhpDto\Enums\LetterCase;

#[Attribute(Attribute::TARGET_CLASS)]
class MapName
{
    /**
     * @param LetterCase|list<LetterCase> $from
     */
    public function __construct(
        protected(set) LetterCase|array $from,
        protected(set) LetterCase $to = LetterCase::CAMEL
    ) {
    }
}
