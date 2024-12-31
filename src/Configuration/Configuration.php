<?php

namespace Nuxtifyts\PhpDto\Configuration;

use Nuxtifyts\PhpDto\Exceptions\DataConfigurationException;

interface Configuration
{
    /**
     * @param ?array<string, mixed> $config
     *
     * @throws DataConfigurationException
     */
    public static function getInstance(
        ?array $config = null,
        bool $forceCreate = false
    ): self;
}
