<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Serializers;

use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\Enums\Property\Type;
use Nuxtifyts\PhpDto\Exceptions\DeserializeException;
use Nuxtifyts\PhpDto\Serializers\DataSerializer;
use Nuxtifyts\PhpDto\Serializers\Serializer;
use Nuxtifyts\PhpDto\Tests\Dummies\AddressData;
use Nuxtifyts\PhpDto\Tests\Dummies\CoordinatesData;
use Nuxtifyts\PhpDto\Tests\Dummies\CountryData;
use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use ReflectionClass;
use Throwable;

#[CoversClass(Serializer::class)]
#[CoversClass(DataSerializer::class)]
#[CoversClass(PropertyContext::class)]
#[UsesClass(PropertyContext::class)]
#[UsesClass(AddressData::class)]
#[UsesClass(CountryData::class)]
final class DataSerializerTest extends UnitCase
{
    /**
     * @throws Throwable
     */
    #[Test]
    public function supported_data_type(): void
    {
        self::assertEquals(
            [
                Type::DATA
            ],
            DataSerializer::supportedTypes()
        );
    }

    /**
     * @throws Throwable
     */
    #[Test]
    public function will_perform_data_serialization_on_data_types_data(): void
    {
        $addressData = new AddressData(
            'street',
            'city',
            'state',
            'zip',
            new CountryData(
                'name',
                'code'
            ),
            new CoordinatesData(
                1.0,
                2.0
            )
        );
        $addressData2 = new AddressData(
            'street2',
            'city2',
            'state2',
            'zip2',
            new CountryData(
                'name2',
                'code2'
            ),
            null
        );

        $reflectionClass = new ReflectionClass($addressData);
        $property = $reflectionClass->getProperty('coordinates');

        $dataSerializer = new DataSerializer();

        self::assertEquals(
            [
                'coordinates' => [
                    'latitude' => 1.0,
                    'longitude' => 2.0,
                    'radius' => null
                ]
            ],
            $dataSerializer->serialize(PropertyContext::getInstance($property), $addressData)
        );

        self::assertEquals(
            [
                'coordinates' => null
            ],
            $dataSerializer->serialize(PropertyContext::getInstance($property), $addressData2)
        );

        $serializedData = [
            'coordinates' => [
                'latitude' => 1.0,
                'longitude' => 2.0
            ]
        ];

        self::assertEquals(
            new CoordinatesData(
                1.0,
                2.0
            ),
            $dataSerializer->deserialize(PropertyContext::getInstance($property), $serializedData)
        );

        self::assertEquals(
            null,
            $dataSerializer->deserialize(PropertyContext::getInstance($property), ['coordinates' => null])
        );
    }

    /**
     * @throws Throwable
     */
    #[Test]
    public function will_throw_exception_when_property_is_not_nullable_and_it_fails_to_resolve_base_data_instance(): void
    {
        $data = [
            'country' => null
        ];

        $reflectionClass = new ReflectionClass(AddressData::class);
        $property = $reflectionClass->getProperty('country');

        self::expectException(DeserializeException::class);
        new DataSerializer()->deserialize(PropertyContext::getInstance($property), $data);
    }

    /**
     * @throws Throwable
     */
    #[Test]
    public function will_throw_an_exception_when_deserializing_using_incomplete_array(): void
    {
        $data = [
            'country' => [
                'name' => 'name'
            ]
        ];

        $reflectionClass = new ReflectionClass(AddressData::class);
        $property = $reflectionClass->getProperty('country');

        self::expectException(DeserializeException::class);
        new DataSerializer()->deserialize(PropertyContext::getInstance($property), $data);
    }
}
