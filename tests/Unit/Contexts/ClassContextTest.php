<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Contexts;

use Nuxtifyts\PhpDto\Tests\Dummies\DummyWithNormalizerData;
use Nuxtifyts\PhpDto\Tests\Dummies\Normalizers\DummyNormalizer;
use Nuxtifyts\PhpDto\Tests\Dummies\Normalizers\HumanToPersonNormalizer;
use Nuxtifyts\PhpDto\Attributes\Class\WithNormalizer;
use Nuxtifyts\PhpDto\Tests\Dummies\PersonData;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use Nuxtifyts\PhpDto\Contexts\ClassContext;
use PHPUnit\Framework\Attributes\UsesClass;
use Throwable;
use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;

#[CoversClass(ClassContext::class)]
#[CoversClass(WithNormalizer::class)]
#[UsesClass(HumanToPersonNormalizer::class)]
#[UsesClass(DummyNormalizer::class)]
#[UsesClass(PersonData::class)]
#[UsesClass(DummyWithNormalizerData::class)]
final class ClassContextTest extends UnitCase
{
    /**
     * @throws Throwable
     */
    #[Test]
    public function can_create_an_instance_from_reflection_class(): void
    {
        $classContext = ClassContext::getInstance(PersonData::class);

        self::assertInstanceOf(ClassContext::class, $classContext);
    }

    /**
     * @throws Throwable
     */
    #[Test]
    public function can_retrieve_same_instance_of_class(): void
    {
        $classContext = ClassContext::getInstance(PersonData::class);
        $classContext2 = ClassContext::getInstance(PersonData::class);

        self::assertSame($classContext, $classContext2);
    }

    /**
     * @throws Throwable
     */
    #[Test]
    public function can_create_an_instance_from_the_reflection_class(): void
    {
        $classContext = ClassContext::getInstance(PersonData::class);

        self::assertInstanceOf(PersonData::class, $classContext->newInstanceWithoutConstructor());
    }

    /**
     * @throws Throwable
     */
    #[Test]
    public function will_sync_normalizers_from_attribute(): void
    {
        $classContext = ClassContext::getInstance(PersonData::class);

        self::assertEquals(
            [DummyNormalizer::class, HumanToPersonNormalizer::class],
            $classContext->normalizers
        );

        $classContext = ClassContext::getInstance(DummyWithNormalizerData::class);

        self::assertEquals(
            [DummyNormalizer::class],
            $classContext->normalizers
        );
    }
}
