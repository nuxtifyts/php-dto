<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Attributes;

use InvalidArgumentException;
use Nuxtifyts\PhpDto\Attributes\PropertyAttributes\Types\ArrayOfScalarTypes;
use Nuxtifyts\PhpDto\Enums\Property\Type;
use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(ArrayOfScalarTypes::class)]
final class ArrayOfScalarTypeTest extends UnitCase
{
    #[Test]
    public function will_default_to_all_scalar_types_if_no_type_is_specified(): void
    {
        $attribute = new ArrayOfScalarTypes();

        self::assertEquals(Type::SCALAR_TYPES, $attribute->types);
    }

    #[Test]
    public function will_accept_single_and_many_types(): void
    {
        $attribute = new ArrayOfScalarTypes(Type::INT);

        self::assertEquals([Type::INT], $attribute->types);

        $attribute = new ArrayOfScalarTypes([Type::INT, Type::FLOAT]);

        self::assertEquals([Type::INT, Type::FLOAT], $attribute->types);
    }

    #[Test]
    public function will_throw_invalid_argument_exception_if_invalid_type_is_passed(): void
    {
        self::expectException(InvalidArgumentException::class);

        new ArrayOfScalarTypes(Type::DATA);
    }
}
