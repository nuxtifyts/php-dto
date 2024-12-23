<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Serializers;

use DateTimeImmutable;
use Nuxtifyts\PhpDto\Attributes\PropertyAttributes\Types\ArrayOfScalarTypes;
use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\Contexts\TypeContext;
use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Enums\Property\Type;
use Nuxtifyts\PhpDto\Serializers\ArraySerializer;
use Nuxtifyts\PhpDto\Serializers\BackedEnumSerializer;
use Nuxtifyts\PhpDto\Serializers\DataSerializer;
use Nuxtifyts\PhpDto\Serializers\DateTimeSerializer;
use Nuxtifyts\PhpDto\Serializers\ScalarTypeSerializer;
use Nuxtifyts\PhpDto\Serializers\Serializer;
use Nuxtifyts\PhpDto\Tests\Dummies\ArrayOfAttributesData;
use Nuxtifyts\PhpDto\Tests\Dummies\ArrayOfMixedAttributesData;
use Nuxtifyts\PhpDto\Tests\Dummies\Enums\YesNoBackedEnum;
use Nuxtifyts\PhpDto\Tests\Dummies\PersonData;
use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use ReflectionClass;
use Throwable;

#[CoversClass(ArraySerializer::class)]
#[CoversClass(ScalarTypeSerializer::class)]
#[CoversClass(BackedEnumSerializer::class)]
#[CoversClass(DateTimeSerializer::class)]
#[CoversClass(DataSerializer::class)]
#[CoversClass(Serializer::class)]
#[CoversClass(PropertyContext::class)]
#[CoversClass(TypeContext::class)]
#[UsesClass(ArrayOfAttributesData::class)]
#[UsesClass(ArrayOfMixedAttributesData::class)]
final class ArraySerializerTest extends UnitCase
{
    #[Test]
    public function supports_array_types(): void
    {
        self::assertEquals(
            [
                Type::ARRAY
            ],
            ArraySerializer::supportedTypes()
        );
    }

    /**
     * @throws Throwable
     */
    #[Test]
    public function is_able_to_handle_null_if_property_is_nullable(): void
    {
        $object = new readonly class (null) extends Data {
            /**
             * @param ?list<int> $arrayOfIntegers
             */
            public function __construct(
                #[ArrayOfScalarTypes(Type::INT)]
                public ?array $arrayOfIntegers
            ) {
            }
        };

        $reflectionClass = new ReflectionClass($object);
        $property = $reflectionClass->getProperty('arrayOfIntegers');

        $arraySerializer = new ArraySerializer();

        self::assertEquals(
            ['arrayOfIntegers' => null],
            $arraySerializer->serialize(PropertyContext::getInstance($property), $object)
        );

        self::assertEquals(
            null,
            $arraySerializer->deserialize(PropertyContext::getInstance($property), ['arrayOfIntegers' => null])
        );
    }

    /**
     * @param array<string, mixed> $expectedSerializedValue
     *
     * @throws Throwable
     */
    #[Test]
    #[DataProvider('will_perform_data_serialization_on_array_types_data_provider')]
    public function will_perform_data_serialization_on_array_types_data(
        object $object,
        array $expectedSerializedValue,
        string $propertyName,
        mixed $expectedDeserializedValue
    ): void {
        $reflectionClass = new ReflectionClass($object);
        $property = $reflectionClass->getProperty($propertyName);

        $arraySerializer = new ArraySerializer();

        self::assertEquals(
            $expectedSerializedValue,
            $arraySerializer->serialize(PropertyContext::getInstance($property), $object)
        );

        self::assertEquals(
            $expectedDeserializedValue,
            $arraySerializer->deserialize(PropertyContext::getInstance($property), $expectedSerializedValue)
        );
    }

    /**
     * @return array<string, array{
     *     object: mixed,
     *     expectedSerializedValue: array<string, mixed>,
     *     propertyName: string
     * }>
     */
    public static function will_perform_data_serialization_on_array_types_data_provider(): array
    {
        return [
            'Array of integers' => [
                'object' => new ArrayOfAttributesData(arrayOfIntegers: [1, 2, 3]),
                'expectedSerializedValue' => [
                    'arrayOfIntegers' => [1, 2, 3]
                ],
                'propertyName' => 'arrayOfIntegers',
                'expectedDeserializedValue' => [1, 2, 3]
            ],
            'Array of strings' => [
                'object' => new ArrayOfAttributesData(arrayOfStrings: ['a', 'b', 'c']),
                'expectedSerializedValue' => [
                    'arrayOfStrings' => ['a', 'b', 'c']
                ],
                'propertyName' => 'arrayOfStrings',
                'expectedDeserializedValue' => ['a', 'b', 'c']
            ],
            'Array of floats' => [
                'object' => new ArrayOfAttributesData(arrayOfFloats: [1.1, 2.2, 3.3]),
                'expectedSerializedValue' => [
                    'arrayOfFloats' => [1.1, 2.2, 3.3]
                ],
                'propertyName' => 'arrayOfFloats',
                'expectedDeserializedValue' => [1.1, 2.2, 3.3]
            ],
            'Array of booleans' => [
                'object' => new ArrayOfAttributesData(arrayOfBooleans: [true, false, true]),
                'expectedSerializedValue' => [
                    'arrayOfBooleans' => [true, false, true]
                ],
                'propertyName' => 'arrayOfBooleans',
                'expectedDeserializedValue' => [true, false, true]
            ],

            'Array of backed enums' => [
                'object' => new ArrayOfAttributesData(arrayOfBackedEnums: [YesNoBackedEnum::YES, YesNoBackedEnum::NO]),
                'expectedSerializedValue' => [
                    'arrayOfBackedEnums' => [YesNoBackedEnum::YES->value, YesNoBackedEnum::NO->value]
                ],
                'propertyName' => 'arrayOfBackedEnums',
                'expectedDeserializedValue' => [YesNoBackedEnum::YES, YesNoBackedEnum::NO]
            ],
            'Array of date times' => [
                'object' => new ArrayOfAttributesData(arrayOfDateTimes: [new DateTimeImmutable('2021-01-01T00:00:00+00:00')]),
                'expectedSerializedValue' => [
                    'arrayOfDateTimes' => ['2021-01-01T00:00:00+00:00']
                ],
                'propertyName' => 'arrayOfDateTimes',
                'expectedDeserializedValue' => [new DateTimeImmutable('2021-01-01T00:00:00+00:00')]
            ],
            'Array of data' => [
                'object' => new ArrayOfAttributesData(arrayOfPersonData: [new PersonData('John', 'Doe')]),
                'expectedSerializedValue' => [
                    'arrayOfPersonData' => [
                        ['firstName' => 'John', 'lastName' => 'Doe', 'fullName' => 'John Doe']
                    ]
                ],
                'propertyName' => 'arrayOfPersonData',
                'expectedDeserializedValue' => [new PersonData('John', 'Doe')]
            ],
            'Array of mixed attributes' => [
                'object' => new ArrayOfMixedAttributesData(
                    arrayOfIntegersOrBackedEnums: [1, 2],
                ),
                'expectedSerializedValue' => [
                    'arrayOfIntegersOrBackedEnums' => [1, 2]
                ],
                'propertyName' => 'arrayOfIntegersOrBackedEnums',
                'expectedDeserializedValue' => [1, 2]
            ],
            'Array of mixed attributes part 2' => [
                'object' => new ArrayOfMixedAttributesData(
                    arrayOfIntegersOrBackedEnums: [YesNoBackedEnum::YES, YesNoBackedEnum::NO],
                ),
                'expectedSerializedValue' => [
                    'arrayOfIntegersOrBackedEnums' => [YesNoBackedEnum::YES->value, YesNoBackedEnum::NO->value]
                ],
                'propertyName' => 'arrayOfIntegersOrBackedEnums',
                'expectedDeserializedValue' => [YesNoBackedEnum::YES, YesNoBackedEnum::NO]
            ],
            'Associated array of data' => [
                'object' => new ArrayOfAttributesData(
                    arrayOfPersonData: [
                        'john-doe' => new PersonData('John', 'Doe'),
                        'jane-doe' => new PersonData('Jane', 'Doe'),
                    ]
                ),
                'expectedSerializedValue' => [
                    'arrayOfPersonData' => [
                        'john-doe' => ['firstName' => 'John', 'lastName' => 'Doe', 'fullName' => 'John Doe'],
                        'jane-doe' => ['firstName' => 'Jane', 'lastName' => 'Doe', 'fullName' => 'Jane Doe'],
                    ]
                ],
                'propertyName' => 'arrayOfPersonData',
                'expectedDeserializedValue' => [
                    'john-doe' => new PersonData('John', 'Doe'),
                    'jane-doe' => new PersonData('Jane', 'Doe'),
                ]
            ]
        ];
    }
}
