<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Contexts;

use Nuxtifyts\PhpDto\Tests\Dummies\PersonData;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use Nuxtifyts\PhpDto\Contexts\ClassContext;
use ReflectionClass;
use Throwable;

use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;

#[CoversClass(ClassContext::class)]
final class ClassContextTest extends UnitCase
{
    /**
     * @throws Throwable
     */
    #[Test]
    public function can_create_an_instance_from_reflection_class(): void
    {
        $reflectionClass = new ReflectionClass(PersonData::class);
        $classContext = ClassContext::getInstance($reflectionClass);

        self::assertInstanceOf(ClassContext::class, $classContext);
    }

    /**
     * @throws Throwable
     */
    #[Test]
    public function can_retrieve_same_instance_of_class(): void
    {
        $reflectionClass = new ReflectionClass(PersonData::class);
        $classContext = ClassContext::getInstance($reflectionClass);
        $classContext2 = ClassContext::getInstance($reflectionClass);

        self::assertSame($classContext, $classContext2);
    }

    /**
     * @throws Throwable
     */
    #[Test]
    public function can_create_an_instance_from_the_reflection_class(): void
    {
        $reflectionClass = new ReflectionClass(PersonData::class);
        $classContext = ClassContext::getInstance($reflectionClass);

        self::assertInstanceOf(PersonData::class, $classContext->newInstanceWithoutConstructor());
    }
}
