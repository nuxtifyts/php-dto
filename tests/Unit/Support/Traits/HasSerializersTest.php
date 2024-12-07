<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Support\Traits;

use Nuxtifyts\PhpDto\Support\Traits\HasSerializers;
use Nuxtifyts\PhpDto\Tests\Dummies\PersonData;
use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;
use Nuxtifyts\PhpDto\Tests\Dummies\Support\HasSerializersDummy;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use Nuxtifyts\PhpDto\Exceptions\UnknownPropertyException;
use ReflectionProperty;

#[CoversClass(HasSerializers::class)]
#[CoversClass(UnknownPropertyException::class)]
#[UsesClass(HasSerializersDummy::class)]
final class HasSerializersTest extends UnitCase
{
    #[Test]
    public function will_throw_an_exception_if_property_is_unknown(): void
    {
        $person = new PersonData('John', 'Doe');

        self::expectException(UnknownPropertyException::class);

        $hasSerializersDummy = new HasSerializersDummy();

        $hasSerializersDummy->testResolveSerializer(
            new ReflectionProperty(PersonData::class, 'firstName'),
            $person
        );
    }
}
