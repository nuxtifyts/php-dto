<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Concerns;

use Nuxtifyts\PhpDto\Concerns\BaseData;
use Nuxtifyts\PhpDto\Exceptions\DeserializeException;
use Nuxtifyts\PhpDto\Exceptions\SerializeException;
use Nuxtifyts\PhpDto\Support\Traits\HasSerializers;
use Nuxtifyts\PhpDto\Tests\Dummies\CoordinatesData;
use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;
use Nuxtifyts\PhpDto\Tests\Dummies\PersonData;
use Nuxtifyts\PhpDto\Tests\Dummies\UnionTypedData;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use Throwable;

#[CoversClass(BaseData::class)]
#[CoversClass(DeserializeException::class)]
#[CoversClass(SerializeException::class)]
#[CoversClass(HasSerializers::class)]
#[UsesClass(PersonData::class)]
#[UsesClass(UnionTypedData::class)]
final class BaseDataTest extends UnitCase
{
    /**
     * @throws Throwable
     */
    #[Test]
    public function base_data_supports_scalar_types(): void
    {
        $person = new PersonData('John', 'Doe');

        self::assertEquals(
            [
                'firstName' => 'John',
                'lastName' => 'Doe',
                'fullName' => 'John Doe'
            ],
            $personData = $person->jsonSerialize()
        );

        $person = PersonData::from($personData);

        self::assertEquals('John', $person->firstName);
        self::assertEquals('Doe', $person->lastName);
        self::assertEquals('John Doe', $person->fullName);

        $coordinates = CoordinatesData::from('{"latitude": 42.42, "longitude": 24.24}');

        self::assertEquals(42.42, $coordinates->latitude);
        self::assertEquals(24.24, $coordinates->longitude);
        self::assertNull($coordinates->radius);
    }

    /**
     * @throws Throwable
     */
    #[Test]
    public function will_throw_an_exception_if_it_fails_to_resolve_a_serializer(): void
    {
        self::markTestIncomplete('This test is not yet implemented');
        // A class with date time property and no serializer for that property
//        $object = new readonly class (new DateTimeImmutable()) extends Data {
//            public function __construct(
//                public DateTimeInterface $time
//            ) {
//            }
//
//            /**
//             * @inheritDoc
//             */
//            protected static function serializers(): array
//            {
//                return [
//                    ScalarTypeSerializer::class
//                ];
//            }
//        };
//
//        self::expectException(SerializeException::class);
//
//        $object->jsonSerialize();
    }

    /**
     * @throws Throwable
     */
    #[Test]
    public function will_throw_an_exception_if_an_invalid_value_is_passed_to_from_function(): void
    {
        self::expectException(DeserializeException::class);
        self::expectExceptionCode(DeserializeException::INVALID_VALUE_ERROR_CODE);

        PersonData::from(false);
    }

    /**
     * @throws Throwable
     */
    #[Test]
    #[DataProvider('base_data_support_union_types_provider')]
    public function base_data_support_union_types(
        int|string|null $value,
        int|string|null $expected
    ): void {
        $union = new UnionTypedData($value);

        self::assertEquals(
            [
                'value' => $expected
            ],
            $unionData = $union->jsonSerialize()
        );

        $union = UnionTypedData::from($unionData);

        self::assertEquals($expected, $union->value);
    }

    /**
     * @phpstan-ignore-next-line
     */
    public static function base_data_support_union_types_provider(): array
    {
        return [
            'Integer type' => [42, 42],
            'String type' => ['string value', 'string value'],
            'Null type' => [null, null]
        ];
    }
}
