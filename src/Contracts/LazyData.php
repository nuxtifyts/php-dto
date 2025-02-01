<?php

namespace Nuxtifyts\PhpDto\Contracts;

use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Exceptions\DataCreationException;

interface LazyData
{
    /**
     * @throws DataCreationException
     */
    public static function createLazy(mixed ...$args): static;

    /**
     * @param callable(static $data): static $callable
     *
     * @throws DataCreationException
     */
    public static function createLazyUsing(callable $callable): static;
}
