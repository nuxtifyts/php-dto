<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Attributes;

use InvalidArgumentException;
use Nuxtifyts\PhpDto\Attributes\Property\Types\ArrayOfDateTimes;
use Nuxtifyts\PhpDto\Tests\Dummies\PersonData;
use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(ArrayOfDateTimes::class)]
final class ArrayOfDateTimesTest extends UnitCase
{
    #[Test]
    public function will_not_allow_empty_date_time_classes_to_be_passed(): void
    {
        self::expectException(InvalidArgumentException::class);

        new ArrayOfDateTimes([]);
    }

    #[Test]
    public function will_throw_an_exception_if_passed_class_is_not_Datetime_class_or_interface(): void
    {
        self::expectException(InvalidArgumentException::class);

        // @phpstan-ignore-next-line INTENTIONALLY PASSING A STRING TO TEST EXCEPTION
        new ArrayOfDateTimes(PersonData::class);
    }

    #[Test]
    public function wilL_throw_an_exception_if_class_does_not_even_exit(): void
    {
        self::expectException(InvalidArgumentException::class);

        // @phpstan-ignore-next-line INTENTIONALLY PASSING A STRING TO TEST EXCEPTION
        new ArrayOfDateTimes('NonExistentClass');
    }
}
