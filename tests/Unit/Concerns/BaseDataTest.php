<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Concerns;

use DateTimeImmutable;
use DateTimeInterface;
use Nuxtifyts\PhpDto\Attributes\Property\CipherTarget;
use Nuxtifyts\PhpDto\Attributes\Property\Computed;
use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Exceptions\DataCreationException;
use Nuxtifyts\PhpDto\Exceptions\DeserializeException;
use Nuxtifyts\PhpDto\Exceptions\SerializeException;
use Nuxtifyts\PhpDto\Pipelines\DeserializePipeline\DeserializePipeline;
use Nuxtifyts\PhpDto\Pipelines\DeserializePipeline\DeserializePipelinePassable;
use Nuxtifyts\PhpDto\Pipelines\DeserializePipeline\Pipes\RefineDataPipe;
use Nuxtifyts\PhpDto\Serializers\BackedEnumSerializer;
use Nuxtifyts\PhpDto\Serializers\DataSerializer;
use Nuxtifyts\PhpDto\Serializers\DateTimeSerializer;
use Nuxtifyts\PhpDto\Support\Passable;
use Nuxtifyts\PhpDto\Support\Pipe;
use Nuxtifyts\PhpDto\Support\Pipeline;
use Nuxtifyts\PhpDto\Tests\Dummies\AddressData;
use Nuxtifyts\PhpDto\Tests\Dummies\ComputedPropertiesData;
use Nuxtifyts\PhpDto\Tests\Dummies\CoordinatesData;
use Nuxtifyts\PhpDto\Tests\Dummies\CountryData;
use Nuxtifyts\PhpDto\Tests\Dummies\DataCiphers\UselessDataCipher;
use Nuxtifyts\PhpDto\Tests\Dummies\Enums\YesNoBackedEnum;
use Nuxtifyts\PhpDto\Tests\Dummies\FallbackResolvers\DummyPointsFallbackResolver;
use Nuxtifyts\PhpDto\Tests\Dummies\InvitationData;
use Nuxtifyts\PhpDto\Tests\Dummies\PersonData;
use Nuxtifyts\PhpDto\Tests\Dummies\PointData;
use Nuxtifyts\PhpDto\Tests\Dummies\PointGroupData;
use Nuxtifyts\PhpDto\Tests\Dummies\RefundableItemData;
use Nuxtifyts\PhpDto\Tests\Dummies\UnionMultipleComplexData;
use Nuxtifyts\PhpDto\Tests\Dummies\UnionMultipleTypeData;
use Nuxtifyts\PhpDto\Tests\Dummies\UnionTypedData;
use Nuxtifyts\PhpDto\Tests\Dummies\UserData;
use Nuxtifyts\PhpDto\Tests\Dummies\UserGroupData;
use Nuxtifyts\PhpDto\Tests\Dummies\UserLocationData;
use Nuxtifyts\PhpDto\Tests\Dummies\YesOrNoData;
use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use Throwable;

#[CoversClass(Data::class)]
#[CoversClass(DeserializeException::class)]
#[CoversClass(SerializeException::class)]
#[CoversClass(Pipeline::class)]
#[CoversClass(Pipe::class)]
#[CoversClass(DeserializePipelinePassable::class)]
#[CoversClass(DeserializePipeline::class)]
#[CoversClass(RefineDataPipe::class)]
#[CoversClass(Passable::class)]
#[CoversClass(DateTimeSerializer::class)]
#[CoversClass(DataSerializer::class)]
#[CoversClass(BackedEnumSerializer::class)]
#[CoversClass(DataCreationException::class)]
#[UsesClass(PersonData::class)]
#[UsesClass(UnionTypedData::class)]
#[UsesClass(YesOrNoData::class)]
#[UsesClass(InvitationData::class)]
#[UsesClass(CoordinatesData::class)]
#[UsesClass(RefundableItemData::class)]
#[UsesClass(UnionMultipleTypeData::class)]
#[UsesClass(AddressData::class)]
#[UsesClass(CountryData::class)]
#[UsesClass(UnionMultipleComplexData::class)]
#[UsesClass(UserLocationData::class)]
#[UsesClass(UserGroupData::class)]
#[UsesClass(ComputedPropertiesData::class)]
#[UsesClass(PointGroupData::class)]
#[UsesClass(PointData::class)]
#[UsesClass(DummyPointsFallbackResolver::class)]
#[UsesClass(UselessDataCipher::class)]
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
                'lastName' => 'Doe'
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
    public function will_throw_an_exception_if_an_invalid_value_is_passed_to_from_function(): void
    {
        self::expectException(DeserializeException::class);
        self::expectExceptionCode(0);

        PersonData::from(false);
    }

    /**
     * @throws Throwable
     */
    #[Test]
    public function will_throw_deserialization_exception_when_invalid_parameter_is_used_with_computed_properties(): void
    {
        $object = new readonly class ('a', 'b') extends Data {
            #[Computed]
            public string $c;

            public function __construct(
                public string $a,
                string $b
            ) {
                $this->c = $a . $b;
            }
        };

        self::expectException(DeserializeException::class);
        $object::from([
            'a' => 'a',
            'b' => 'b'
        ]);
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

    /**
     * @param class-string<Data> $dtoClass
     * @param array<string, mixed> $data
     * @param array<string, mixed> $expectedProperties
     * @param array<string, mixed> $expectedSerializedData
     *
     * @throws Throwable
     */
    #[Test]
    #[DataProvider('will_perform_serialization_and_deserialization_data_provider')]
    public function will_perform_serialization_and_deserialization_data(
        string $dtoClass,
        array $data,
        array $expectedProperties,
        array $expectedSerializedData,
    ): void {
        $dtoObject = $dtoClass::from($data);

        foreach ($expectedProperties as $property => $value) {
            self::assertEquals($value, $dtoObject->{$property});
        }

        self::assertEquals(
            $expectedSerializedData,
            $dtoObject->jsonSerialize()
        );
    }

    /**
     * @return array<string, mixed>
     */
    public static function will_perform_serialization_and_deserialization_data_provider(): array
    {
        return [
            'Person data' => [
                'dtoClass' => PersonData::class,
                'data' => $data = [
                    'firstName' => 'John',
                    'lastName' => 'Doe'
                ],
                'expectedProperties' => [
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'fullName' => 'John Doe'
                ],
                'expectedSerializedData' => $data
            ],
            'Coordinates' => [
                'dtoClass' => CoordinatesData::class,
                'data' => $data = [
                    'latitude' => 42.42,
                    'longitude' => 24.24
                ],
                'expectedProperties' => [
                    'latitude' => 42.42,
                    'longitude' => 24.24,
                    'radius' => null
                ],
                'expectedSerializedData' => [
                    ...$data,
                    'radius' => null
                ]
            ],
            'YesNo data' => [
                'dtoClass' => YesOrNoData::class,
                'data' => $data = [
                    'yesNo' => YesNoBackedEnum::YES->value
                ],
                'expectedProperties' => [
                    'yesNo' => YesNoBackedEnum::YES
                ],
                'expectedSerializedData' => $data
            ],
            'Invitation data' => [
                'dtoClass' => InvitationData::class,
                'data' => $data = [
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'isComing' => YesNoBackedEnum::YES->value
                ],
                'expectedProperties' => [
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'isComing' => YesNoBackedEnum::YES
                ],
                'expectedSerializedData' => $data
            ],
            'Union typed data' => [
                'dtoClass' => UnionMultipleTypeData::class,
                'data' => $data = [
                    'value' => 123,
                    'yesOrNo' => YesNoBackedEnum::YES->value
                ],
                'expectedProperties' => [
                    'value' => 123,
                    'yesOrNo' => YesNoBackedEnum::YES
                ],
                'expectedSerializedData' => $data
            ],
            'Union typed data 2' => [
                'dtoClass' => UnionMultipleTypeData::class,
                'data' => $data = [
                    'value' => 'string value',
                    'yesOrNo' => false
                ],
                'expectedProperties' => [
                    'value' => 'string value',
                    'yesOrNo' => false
                ],
                'expectedSerializedData' => $data
            ],
            'Refundable item data' => [
                'dtoClass' => RefundableItemData::class,
                'data' => $data = [
                    'id' => 'id',
                    'refundable' => YesNoBackedEnum::YES->value,
                    'refundableUntil' => '2021-01-01T00:00:00+00:00'
                ],
                'expectedProperties' => [
                    'id' => 'id',
                    'refundable' => YesNoBackedEnum::YES,
                    'refundableUntil' => new DateTimeImmutable('2021-01-01T00:00:00+00:00')
                ],
                'expectedSerializedData' => $data
            ],
            'Refundable item data 2' => [
                'dtoClass' => RefundableItemData::class,
                'data' => $data = [
                    'id' => 'id2',
                    'refundable' => YesNoBackedEnum::NO->value,
                    'refundableUntil' => null
                ],
                'expectedProperties' => [
                    'id' => 'id2',
                    'refundable' => YesNoBackedEnum::NO,
                    'refundableUntil' => null
                ],
                'expectedSerializedData' => $data
            ],
            'Refundable item data 3' => [
                'dtoClass' => RefundableItemData::class,
                'data' => $data = [
                    'id' => 'id2',
                    'refundable' => YesNoBackedEnum::NO,
                    'refundableUntil' => null
                ],
                'expectedProperties' => [
                    'id' => 'id2',
                    'refundable' => YesNoBackedEnum::NO,
                    'refundableUntil' => null
                ],
                'expectedSerializedData' => [
                    ...$data,
                    'refundable' => YesNoBackedEnum::NO->value
                ]
            ],
            'Refundable item data 4' => [
                'dtoClass' => RefundableItemData::class,
                'data' => $data = [
                    'id' => 'id',
                    'refundable' => YesNoBackedEnum::YES->value,
                    'refundableUntil' => new DateTimeImmutable('2021-01-01T00:00:00+00:00')
                ],
                'expectedProperties' => [
                    'id' => 'id',
                    'refundable' => YesNoBackedEnum::YES,
                    'refundableUntil' => new DateTimeImmutable('2021-01-01T00:00:00+00:00')
                ],
                'expectedSerializedData' => [
                    ...$data,
                    'refundableUntil' => new DateTimeImmutable('2021-01-01T00:00:00+00:00')
                        ->format(DateTimeInterface::ATOM)
                ]
            ],
            'Address data 1' => [
                'dtoClass' => AddressData::class,
                'data' => $data = [
                    'street' => 'street',
                    'city' => 'city',
                    'state' => 'state',
                    'zip' => 'zip',
                    'country' => [
                        'name' => 'country name',
                        'code' => 'country code'
                    ],
                    'coordinates' => [
                        'latitude' => 42.42,
                        'longitude' => 24.24
                    ]
                ],
                'expectedProperties' => [
                    'street' => 'street',
                    'city' => 'city',
                    'state' => 'state',
                    'zip' => 'zip',
                    'country' => new CountryData('country code', 'country name'),
                    'coordinates' => new CoordinatesData(42.42, 24.24)
                ],
                'expectedSerializedData' => [
                    ...$data,
                    'coordinates' => [
                        'latitude' => 42.42,
                        'longitude' => 24.24,
                        'radius' => null
                    ]
                ]
            ],
            'Address data 2' => [
                'dtoClass' => AddressData::class,
                'data' => $data = [
                    'street' => 'street 2',
                    'city' => 'city 2',
                    'state' => 'state 2',
                    'zip' => 'zip 2',
                    'country' => [
                        'name' => 'country name 2',
                        'code' => 'country code 2'
                    ],
                    'coordinates' => null
                ],
                'expectedProperties' => [
                    'street' => 'street 2',
                    'city' => 'city 2',
                    'state' => 'state 2',
                    'zip' => 'zip 2',
                    'country' => new CountryData('country code 2', 'country name 2'),
                    'coordinates' => null
                ],
                'expectedSerializedData' => $data
            ],
            'Union multiple type data' => [
                'dtoClass' => UnionMultipleComplexData::class,
                'data' => $data = [
                    'yesOrNo' => YesNoBackedEnum::YES->value,
                    'location' => [
                        'code' => 'country code 3',
                        'name' => 'country name 3'
                    ]
                ],
                'expectedProperties' => [
                    'yesOrNo' => YesNoBackedEnum::YES,
                    'location' => new CountryData('country code 3', 'country name 3')
                ],
                'expectedSerializedData' => $data
            ],
            'Union multiple type data 2' => [
                'dtoClass' => UnionMultipleComplexData::class,
                'data' => $data = [
                    'yesOrNo' => false,
                    'location' => [
                        'street' => 'street 3',
                        'city' => 'city 3',
                        'state' => 'state 3',
                        'zip' => 'zip 3',
                        'country' => [
                            'name' => 'country name 3',
                            'code' => 'country code 3'
                        ],
                        'coordinates' => [
                            'latitude' => 42.42,
                            'longitude' => 24.24
                        ]
                    ]
                ],
                'expectedProperties' => [
                    'yesOrNo' => false,
                    'location' => new AddressData(
                        'street 3',
                        'city 3',
                        'state 3',
                        'zip 3',
                        new CountryData('country code 3', 'country name 3'),
                        new CoordinatesData(42.42, 24.24)
                    )
                ],
                'expectedSerializedData' => [
                    ...$data,
                    'location' => [
                        'street' => 'street 3',
                        'city' => 'city 3',
                        'state' => 'state 3',
                        'zip' => 'zip 3',
                        'country' => [
                            'name' => 'country name 3',
                            'code' => 'country code 3'
                        ],
                        'coordinates' => [
                            'latitude' => 42.42,
                            'longitude' => 24.24,
                            'radius' => null
                        ]
                    ]
                ],
            ],
            'User location data' => [
                'dtoClass' => UserLocationData::class,
                'data' => $data = [
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'address' => [
                        'street' => 'street 4',
                        'city' => 'city 4',
                        'state' => 'state 4',
                        'zip' => 'zip 4',
                        'country' => [
                            'name' => 'country name 4',
                            'code' => 'country code 4'
                        ],
                        'coordinates' => [
                            'latitude' => 42.42,
                            'longitude' => 24.24
                        ]
                    ]
                ],
                'expectedProperties' => [
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'address' => new AddressData(
                        'street 4',
                        'city 4',
                        'state 4',
                        'zip 4',
                        new CountryData('country code 4', 'country name 4'),
                        new CoordinatesData(42.42, 24.24)
                    )
                ],
                'expectedSerializedData' => [
                    ...$data,
                    'address' => [
                        ...$data['address'],
                        'coordinates' => [
                            'latitude' => 42.42,
                            'longitude' => 24.24,
                            'radius' => null
                        ]
                    ]
                ]
            ],
            'User group data' => [
                'dtoClass' => UserGroupData::class,
                'data' => $data = [
                    'name' => 'Group name',
                    'users' => [
                        [
                            'firstName' => 'John',
                            'lastName' => 'Doe',
                        ]
                    ]
                ],
                'expectedProperties' => [
                    'name' => 'Group name',
                    'users' => [
                        new UserData('John', 'Doe')
                    ]
                ],
                'expectedSerializedData' => $data
            ],
            'User group data 2' => [
                'dtoClass' => UserGroupData::class,
                'data' => $data = [
                    'name' => 'Group name 2',
                    'users' => [
                        1,
                        2
                    ]
                ],
                'expectedProperties' => [
                    'name' => 'Group name 2',
                    'users' => [1, 2]
                ],
                'expectedSerializedData' => $data
            ],
            'User group data 3' => [
                'dtoClass' => UserGroupData::class,
                'data' => $data = [
                    'name' => 'Group name 2',
                    'users' => [
                        new UserData('John', 'Doe'),
                        new UserData('Jane', 'Doe')
                    ]
                ],
                'expectedProperties' => [
                    'name' => 'Group name 2',
                    'users' => [
                        new UserData('John', 'Doe'),
                        new UserData('Jane', 'Doe')
                    ]
                ],
                'expectedSerializedData' => [
                    ...$data,
                    'users' => [
                        [
                            'firstName' => 'John',
                            'lastName' => 'Doe'
                        ],
                        [
                            'firstName' => 'Jane',
                            'lastName' => 'Doe'
                        ]
                    ]
                ]
            ],
            'Computed properties data' => [
                'dtoClass' => ComputedPropertiesData::class,
                'data' => $data = [
                    'a' => 'a',
                    'b' => 'b'
                ],
                'expectedProperties' => [
                    'a' => 'a',
                    'b' => 'b',
                    'c' => 'ab'
                ],
                'expectedSerializedData' => $data
            ]
        ];
    }

    #[Test]
    public function will_throw_an_exception_when_property_serialization_fails(): void
    {
        $object = new readonly class ('secret') extends Data {
            public function __construct(
                #[CipherTarget(UselessDataCipher::class)]
                public string $secret
            ) {
            }
        };

        self::expectException(SerializeException::class);
        $object->jsonSerialize();
    }

    #[Test]
    public function will_throw_an_exception_when_invalid_data_is_passed_to_create_function(): void
    {
        self::expectException(DataCreationException::class);

        PointData::create('{"x: 1, "y": 2}');
    }

    /**
     * @throws Throwable
     */
    #[Test]
    public function will_be_able_to_create_an_instance_using_create(): void
    {
        $point = PointData::create(
            x: 1,
            y: 2
        );

        self::assertInstanceOf(PointData::class, $point);
        self::assertEquals(1, $point->x);
        self::assertEquals(2, $point->y);

        $point = PointData::create('{"x": 3, "y": 4}');

        self::assertInstanceOf(PointData::class, $point);
        self::assertEquals(3, $point->x);
        self::assertEquals(4, $point->y);

        // Make sure we skip deciphering the key
        $pointGroup = PointGroupData::create(
            key: 'random-key'
        );

        self::assertInstanceOf(PointGroupData::class, $pointGroup);
        self::assertEquals('random-key', $pointGroup->key);
        self::assertEquals([
            new PointData(1, 2),
            new PointData(3, 4),
            new PointData(5, 6)
        ], $pointGroup->points);
    }
}
