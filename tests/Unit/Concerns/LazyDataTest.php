<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Concerns;

use Nuxtifyts\PhpDto\Contexts\ClassContext;
use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Exceptions\DataCreationException;
use Nuxtifyts\PhpDto\Exceptions\DeserializeException;
use Nuxtifyts\PhpDto\Tests\Dummies\PersonData;
use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\Test;
use Throwable;

#[CoversClass(Data::class)]
#[CoversClass(ClassContext::class)]
#[CoversClass(DataCreationException::class)]
#[UsesClass(PersonData::class)]
#[UsesClass(DeserializeException::class)]
final class LazyDataTest extends UnitCase
{
    /**
     * @throws Throwable
     */
    #[Test]
    public function will_create_lazy_instance(): void
    {
        $person = PersonData::createLazy(
            firstName: 'John',
            lastName: 'Doe'
        );

        self::assertInstanceOf(PersonData::class, $person);
        self::assertEquals('John Doe', $person->fullName);
    }

    /**
     * @throws Throwable
     */
    #[Test]
    public function will_throw_data_creation_exception_if_params_are_invalid(): void
    {
        self::expectException(DataCreationException::class);

        PersonData::createLazy(
            'John',
            'Doe'
        );
    }

    /**
     * @throws Throwable
     */
    #[Test]
    public function will_throw_deserializing_exception_if_properties_are_missing(): void
    {
        $person = PersonData::createLazy(
            firstName: 'John',
        );

        $this->assertInstanceOf(PersonData::class, $person);
        self::expectException(DeserializeException::class);

        /** @phpstan-ignore-next-line */
        $person->fullName;
    }
}
