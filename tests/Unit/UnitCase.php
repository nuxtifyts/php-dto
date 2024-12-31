<?php

namespace Nuxtifyts\PhpDto\Tests\Unit;

use Nuxtifyts\PhpDto\Configuration\DataConfiguration;
use Nuxtifyts\PhpDto\Exceptions\DataConfigurationException;
use PHPUnit\Framework\TestCase;

abstract class UnitCase extends Testcase
{
    /**
     * @throws DataConfigurationException
     */
    protected static function resetConfig(): void
    {
        DataConfiguration::getInstance(forceCreate: true);
    }
}
