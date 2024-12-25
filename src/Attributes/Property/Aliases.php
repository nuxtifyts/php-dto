<?php

namespace Nuxtifyts\PhpDto\Attributes\Property;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Aliases
{
    /** @var list<string> */
    private(set) array $aliases;

    public function __construct(
        string $alias,
        string ...$aliases
    ) {
        $this->aliases = array_values([$alias, ...$aliases]);
    }
}
