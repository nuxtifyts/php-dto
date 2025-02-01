<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Attributes;

use Nuxtifyts\PhpDto\Attributes\Class\Lazy;
use Nuxtifyts\PhpDto\Contexts\ClassContext;
use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Tests\Dummies\LazyDummyData;
use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use Throwable;

#[CoversClass(Lazy::class)]
#[CoversClass(Data::class)]
#[CoversClass(ClassContext::class)]
#[UsesClass(LazyDummyData::class)]
final class LazyTest extends UnitCase
{
    /**
     * @throws Throwable
     */
    #[Test]
    public function will_be_able_to_detect_lazy_data_and_create_it_using_attribute(): void
    {
        $context = ClassContext::getInstance(LazyDummyData::class);

        self::assertTrue($context->isLazy);

        $lazyDummyData = LazyDummyData::create(propertyA: 'a', propertyB: 'b');

        self::assertEquals('a', $lazyDummyData->propertyA);
        self::assertEquals('b', $lazyDummyData->propertyB);

        $lazyDummyData = LazyDummyData::from(['propertyA' => 'a', 'propertyB' => 'b']);
        self::assertEquals('a', $lazyDummyData->propertyA);
        self::assertEquals('b', $lazyDummyData->propertyB);
    }

    /**
     * @throws Throwable
     */
    #[Test]
    public function will_be_able_to_create_empty_instance_of_lazy_data(): void
    {
        $lazyDummyData = LazyDummyData::empty();

        self::assertInstanceOf(LazyDummyData::class, $lazyDummyData);
        self::assertEquals('', $lazyDummyData->propertyA);
        self::assertEquals('', $lazyDummyData->propertyB);
    }

    /**
     * @throws Throwable
     */
    #[Test]
    public function will_be_able_to_clone_instance_of_lazy_data(): void
    {
        $lazyDummyData = LazyDummyData::create(propertyA: 'a', propertyB: 'b');
        $clonedLazyDummyData = $lazyDummyData->with(propertyA: 'c', propertyB: 'd');

        self::assertEquals('c', $clonedLazyDummyData->propertyA);
        self::assertEquals('d', $clonedLazyDummyData->propertyB);
    }
}
