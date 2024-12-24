<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Attributes;

use InvalidArgumentException;
use Nuxtifyts\PhpDto\Attributes\Property\Types\ArrayOfBackedEnums;
use Nuxtifyts\PhpDto\Tests\Dummies\Enums\YesNoEnum;
use Nuxtifyts\PhpDto\Tests\Dummies\PersonData;
use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(ArrayOfBackedEnums::class)]
final class ArrayOfBackedEnumTest extends UnitCase
{
    #[Test]
    public function will_not_allow_empty_backed_enum_classes_to_be_passed(): void
    {
        self::expectException(InvalidArgumentException::class);

        new ArrayOfBackedEnums([]);
    }

    #[Test]
    public function will_throw_an_exception_if_passed_class_is_not_backed_enum_class_or_interface(): void
    {
        self::expectException(InvalidArgumentException::class);

        // @phpstan-ignore-next-line
        new ArrayOfBackedEnums(PersonData::class);
    }

    #[Test]
    public function wilL_throw_an_exception_if_class_does_not_even_exit(): void
    {
        self::expectException(InvalidArgumentException::class);

        // @phpstan-ignore-next-line
        new ArrayOfBackedEnums('NonExistentClass');
    }

    #[Test]
    public function wiLL_throw_an_exception_if_a_non_backed_enum_class_is_passed(): void
    {
        self::expectException(InvalidArgumentException::class);

        // @phpstan-ignore-next-line
        new ArrayOfBackedEnums([YesNoEnum::class]);
    }
}
