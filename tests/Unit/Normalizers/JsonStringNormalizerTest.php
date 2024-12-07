<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Normalizers;

use Nuxtifyts\PhpDto\Normalizers\JsonStringNormalizer;
use Nuxtifyts\PhpDto\Normalizers\Normalizer;
use Nuxtifyts\PhpDto\Tests\Dummies\PersonData;
use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass(Normalizer::class)]
#[CoversClass(JsonStringNormalizer::class)]
#[UsesClass(PersonData::class)]
final class JsonStringNormalizerTest extends UnitCase
{
    #[Test]
    public function will_return_false_when_value_is_not_a_string(): void
    {
        $normalizer = new JsonStringNormalizer(1, PersonData::class);

        self::assertFalse($normalizer->normalize());
    }

    #[Test]
    public function will_return_false_when_value_is_not_a_valid_json_string(): void
    {
        $normalizer = new JsonStringNormalizer('{"test": "test"', PersonData::class);

        self::assertFalse($normalizer->normalize());
    }

    #[Test]
    public function will_return_false_when_value_has_non_string_keys(): void
    {
        $normalizer = new JsonStringNormalizer('{"test": "test", "1": "test"}', PersonData::class);

        self::assertFalse($normalizer->normalize());
    }

    #[Test]
    public function will_return_normalized_array_when_value_is_valid_json_string(): void
    {
        $normalizer = new JsonStringNormalizer(
            '{"firstName": "John", "lastName": "Doe", "fullName": "John Doe"}',
            PersonData::class
        );

        self::assertEquals(
            ['firstName' => 'John', 'lastName' => 'Doe', 'fullName' => 'John Doe'],
            $normalizer->normalize()
        );
    }
}
