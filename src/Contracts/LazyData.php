<?php

namespace Nuxtifyts\PhpDto\Contracts;

use Nuxtifyts\PhpDto\Exceptions\DataCreationException;

interface LazyData
{
    /**
     * @throws DataCreationException
     */
    public static function createLazy(mixed ...$args): static;
}
