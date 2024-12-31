<?php

namespace Nuxtifyts\PhpDto\Tests\Unit;

use Nuxtifyts\PhpDto\Configuration\DataConfiguration;
use Nuxtifyts\PhpDto\Configuration\NormalizersConfiguration;
use Nuxtifyts\PhpDto\Configuration\SerializersConfiguration;
use Nuxtifyts\PhpDto\Exceptions\DataConfigurationException;
use Nuxtifyts\PhpDto\Normalizers\ArrayNormalizer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Throwable;

#[CoversClass(NormalizersConfiguration::class)]
#[CoversClass(SerializersConfiguration::class)]
#[CoversClass(DataConfiguration::class)]
#[CoversClass(DataConfigurationException::class)]
final class DataConfigurationTest extends UnitCase
{
    /**
     * @throws Throwable
     */
    #[Test]
    public function it_can_create_an_instance_and_override_it_if_needed(): void
    {
        $config = DataConfiguration::getInstance();
        self::assertInstanceOf(DataConfiguration::class, $config);

        $sameConfig = DataConfiguration::getInstance();
        self::assertSame($config, $sameConfig);

        $newConfig = DataConfiguration::getInstance(forceCreate: true);
        self::assertNotSame($config, $newConfig);

        self::resetConfig();
    }

    /**
     * @throws Throwable
     */
    #[Test]
    public function can_override_default_configuration(): void
    {
        $config = DataConfiguration::getInstance();

        $baseNormalizers = $config->normalizers->baseNormalizers;

        $config = DataConfiguration::getInstance([
            'normalizers' => [
                'baseNormalizers' => [
                    ArrayNormalizer::class,
                ],
            ],
        ], forceCreate: true);

        self::assertNotEquals($baseNormalizers, $config->normalizers->baseNormalizers);
        self::assertEquals([ArrayNormalizer::class], $config->normalizers->baseNormalizers);

        self::resetConfig();
    }

    /**
     * @throws Throwable
     */
    #[Test]
    public function will_throw_an_exception_if_invalid_normalizers_are_provided(): void
    {
        self::expectException(DataConfigurationException::class);

        DataConfiguration::getInstance([
            'normalizers' => [
                'baseNormalizers' => [
                    'invalidNormalizer',
                ],
            ],
        ], forceCreate: true);
    }

    /**
     * @throws Throwable
     */
    #[Test]
    public function will_throw_an_exception_if_invalid_serializers_are_provided(): void
    {
        self::expectException(DataConfigurationException::class);

        DataConfiguration::getInstance([
            'serializers' => [
                'baseSerializers' => [
                    'invalidSerializer',
                ],
            ],
        ], forceCreate: true);
    }
}
