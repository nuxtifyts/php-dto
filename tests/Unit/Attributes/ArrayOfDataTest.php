<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Attributes;

use InvalidArgumentException;
use Nuxtifyts\PhpDto\Attributes\PropertyAttributes\Types\ArrayOfData;
use Nuxtifyts\PhpDto\Tests\Dummies\Enums\YesNoEnum;
use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(ArrayOfData::class)]
final class ArrayOfDataTest extends UnitCase
{
    #[Test]
    public function will_not_allow_empty_data_classes_to_be_passed(): void
    {
        self::expectException(InvalidArgumentException::class);

        new ArrayOfData([]);
    }

    #[Test]
    public function wilL_throw_an_exception_if_class_does_not_even_exit(): void
    {
        self::expectException(InvalidArgumentException::class);

        // @phpstan-ignore-next-line
        new ArrayOfData('NonExistentClass');
    }

    #[Test]
    public function wiLL_throw_an_exception_if_a_non_data_class_is_passed(): void
    {
        self::expectException(InvalidArgumentException::class);

        // @phpstan-ignore-next-line
        new ArrayOfData([YesNoEnum::class]);
    }
}
