<?php

namespace Nuxtifyts\PhpDto\Contracts;

use Nuxtifyts\PhpDto\Exceptions\DataCreationException;

interface CloneableData
{
    /**
     * @throws DataCreationException
     */
    public function with(mixed ...$args): static;
}
