<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Normalizers;

use Nuxtifyts\PhpDto\Normalizers\StdClassNormalizer;
use Nuxtifyts\PhpDto\Normalizers\Normalizer;
use Nuxtifyts\PhpDto\Tests\Dummies\PersonData;
use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use StdClass;

#[CoversClass(Normalizer::class)]
#[CoversClass(StdClassNormalizer::class)]
#[UsesClass(PersonData::class)]
final class StdClassNormalizerTest extends UnitCase
{
    #[Test]
    public function will_return_false_when_value_is_not_an_std_class(): void
    {
        $normalizer = new StdClassNormalizer('test', PersonData::class);

        self::assertFalse($normalizer->normalize());
    }

    #[Test]
    public function will_return_false_when_std_class_has_non_string_keys(): void
    {
        $stdClass = new StdClass();
        $stdClass->{1} = 'Test';
        $stdClass->lastName = 'Doe';

        $normalizer = new StdClassNormalizer($stdClass, PersonData::class);

        self::assertFalse($normalizer->normalize());
    }

    #[Test]
    public function will_return_normalized_array_when_value_is_std_class(): void
    {
        $stdClass = new StdClass();
        $stdClass->firstName = 'John';
        $stdClass->lastName = 'Doe';
        $stdClass->fullName = 'John Doe';

        $normalizer = new StdClassNormalizer($stdClass, PersonData::class);

        self::assertEquals(
            ['firstName' => 'John', 'lastName' => 'Doe', 'fullName' => 'John Doe'],
            $normalizer->normalize()
        );
    }
}
