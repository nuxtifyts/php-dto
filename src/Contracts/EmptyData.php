<?php

namespace Nuxtifyts\PhpDto\Contracts;

use Nuxtifyts\PhpDto\Exceptions\DataCreationException;

interface EmptyData
{
    /**
     * @throws DataCreationException
     */
    public static function empty(): static;
}
