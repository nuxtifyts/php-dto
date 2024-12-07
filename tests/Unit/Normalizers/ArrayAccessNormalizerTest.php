<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Normalizers;

use Nuxtifyts\PhpDto\Tests\Dummies\ArrayAccessObjects\ArrayAccessPerson;
use Nuxtifyts\PhpDto\Tests\Dummies\ArrayAccessObjects\ArrayAccessNumericKeysClass;
use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Nuxtifyts\PhpDto\Normalizers\ArrayAccessNormalizer;
use Nuxtifyts\PhpDto\Normalizers\Normalizer;
use Nuxtifyts\PhpDto\Tests\Dummies\PersonData;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass(Normalizer::class)]
#[CoversClass(ArrayAccessNormalizer::class)]
#[UsesClass(PersonData::class)]
#[UsesClass(ArrayAccessPerson::class)]
final class ArrayAccessNormalizerTest extends UnitCase
{
    #[Test]
    public function will_return_false_when_value_is_not_an_iterable(): void
    {
        $normalizer = new ArrayAccessNormalizer('test', PersonData::class);

        self::assertFalse($normalizer->normalize());
    }

    #[Test]
    public function will_return_false_when_key_is_not_a_string(): void
    {
        $normalizer = new ArrayAccessNormalizer([1 => 'test'], PersonData::class);

        self::assertFalse($normalizer->normalize());
    }

    #[Test]
    public function will_return_false_when_key_is_not_a_string_in_array_accessed_object(): void
    {
        $arrayAccessPerson = new ArrayAccessNumericKeysClass('John', 'Doe');
        $normalizer = new ArrayAccessNormalizer($arrayAccessPerson, PersonData::class);

        self::assertFalse($normalizer->normalize());
    }

    #[Test]
    public function will_return_normalized_array_when_value_is_iterable(): void
    {
        $normalizer = new ArrayAccessNormalizer(['key' => 'value'], PersonData::class);

        self::assertEquals(['key' => 'value'], $normalizer->normalize());
    }

    #[Test]
    public function will_return_normalized_array_when_value_is_array_accessed(): void
    {
        $arrayAccessPerson = new ArrayAccessPerson('John', 'Doe');
        $normalizer = new ArrayAccessNormalizer($arrayAccessPerson, PersonData::class);

        self::assertEquals(
            ['firstName' => 'John', 'lastName' => 'Doe', 'fullName' => 'John Doe'],
            $normalizer->normalize()
        );
    }
}
