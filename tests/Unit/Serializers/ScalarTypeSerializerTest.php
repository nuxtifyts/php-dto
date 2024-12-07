<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Serializers;

use Nuxtifyts\PhpDto\Exceptions\DeserializeException;
use Nuxtifyts\PhpDto\Tests\Dummies\CoordinatesData;
use Nuxtifyts\PhpDto\Tests\Dummies\PersonData;
use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;
use Nuxtifyts\PhpDto\Serializers\Serializer;
use Nuxtifyts\PhpDto\Serializers\ScalarTypeSerializer;
use Nuxtifyts\PhpDto\Support\Traits\HasSerializers;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Nuxtifyts\PhpDto\Support\Data\DataCacheHelper;
use Nuxtifyts\PhpDto\Helper\ReflectionPropertyHelper;
use PHPUnit\Framework\Attributes\UsesClass;
use ReflectionClass;
use Throwable;

#[CoversClass(ScalarTypeSerializer::class)]
#[CoversClass(Serializer::class)]
#[CoversClass(HasSerializers::class)]
#[CoversClass(DataCacheHelper::class)]
#[CoversClass(ReflectionPropertyHelper::class)]
#[CoversClass(DeserializeException::class)]
#[UsesClass(PersonData::class)]
#[UsesClass(CoordinatesData::class)]
final class ScalarTypeSerializerTest extends UnitCase
{
    /**
     * @throws Throwable
     */
    #[Test]
    public function will_perform_serialize_on_person_data_with_only_scalar_type_properties(): void
    {
        $person = new PersonData('John', 'Doe');
        $reflectionClass = new ReflectionClass($person);

        self::assertTrue(
            ScalarTypeSerializer::isSupported(
                $reflectionClass->getProperty('firstName'),
                $person
            )
        );

        $scalarTypeSerializer = new ScalarTypeSerializer();

        self::assertEquals(
            ['firstName' => 'John'],
            $scalarTypeSerializer->serialize(
                $reflectionClass->getProperty('firstName'),
                $person
            )
        );

        self::assertEquals(
            ['firstName' => 'John', 'lastName' => 'Doe', 'fullName' => 'John Doe'],
            $person->jsonSerialize()
        );
    }

    /**
     * @throws Throwable
     */
    #[Test]
    public function will_perform_deserialize_on_array_data_for_person_data_with_only_scalar_types(): void
    {
        $personArrayData = [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'fullName' => 'John Doe'
        ];

        $reflectionClass = new ReflectionClass(PersonData::class);

        $scalarTypeSerializer = new ScalarTypeSerializer();

        self::assertEquals(
            'John',
            $scalarTypeSerializer->deserialize(
                $reflectionClass->getProperty('firstName'),
                $personArrayData
            )
        );

        self::assertEquals(
            ['firstName' => 'John', 'lastName' => 'Doe', 'fullName' => 'John Doe'],
            PersonData::from($personArrayData)->jsonSerialize()
        );
    }

    /**
     * @throws Throwable
     */
    #[Test]
    public function will_return_null_for_nullable_properties(): void
    {
        $coordinatesArrayData = [
            'latitude' => 1.0,
            'longitude' => 2.0,
            'radius' => null
        ];

        $reflectionClass = new ReflectionClass(CoordinatesData::class);
        $scalarTypeSerializer = new ScalarTypeSerializer();

        self::assertNull(
            $scalarTypeSerializer->deserialize(
                $reflectionClass->getProperty('radius'),
                $coordinatesArrayData
            )
        );

        unset($coordinatesArrayData['radius']);

        self::assertNull(
            $scalarTypeSerializer->deserialize(
                $reflectionClass->getProperty('radius'),
                $coordinatesArrayData
            )
        );

        self::assertEquals(
            ['latitude' => 1.0, 'longitude' => 2.0, 'radius' => null],
            CoordinatesData::from($coordinatesArrayData)->jsonSerialize()
        );
    }

    /**
     * @throws Throwable
     */
    #[Test]
    public function will_throw_an_exception_if_it_fails_to_deserialize(): void
    {
        $coordinatesArrayData = [
            'latitude' => 1.0,
            'longitude' => 2.0,
            'radius' => 'invalid'
        ];

        $reflectionClass = new ReflectionClass(CoordinatesData::class);

        self::expectException(DeserializeException::class);

        $scalarTypeSerializer = new ScalarTypeSerializer();

        $scalarTypeSerializer->deserialize(
            $reflectionClass->getProperty('radius'),
            $coordinatesArrayData
        );
    }
}
