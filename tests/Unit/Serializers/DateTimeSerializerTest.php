<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Serializers;

use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\Enums\Property\Type;
use Nuxtifyts\PhpDto\Exceptions\DeserializeException;
use Nuxtifyts\PhpDto\Serializers\DateTimeSerializer;
use Nuxtifyts\PhpDto\Serializers\Serializer;
use Nuxtifyts\PhpDto\Tests\Dummies\Enums\YesNoBackedEnum;
use Nuxtifyts\PhpDto\Tests\Dummies\UserBirthdateData;
use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use Nuxtifyts\PhpDto\Tests\Dummies\RefundableItemData;
use ReflectionClass;
use DateTimeImmutable;
use Throwable;

#[CoversClass(Serializer::class)]
#[CoversClass(DateTimeSerializer::class)]
#[UsesClass(PropertyContext::class)]
#[UsesClass(YesNoBackedEnum::class)]
#[UsesClass(UserBirthdateData::class)]
final class DateTimeSerializerTest extends UnitCase
{
    /**
     * @throws Throwable
     */
    #[Test]
    public function supported_date_time_type(): void
    {
        self::assertEquals(
            [
                Type::DATETIME
            ],
            DateTimeSerializer::supportedTypes()
        );
    }

    /**
     * @throws Throwable
     */
    #[Test]
    public function will_perform_data_serialization_on_date_time_types_data(): void
    {
        $refundableItemData = new RefundableItemData(
            'id',
            YesNoBackedEnum::YES,
            new DateTimeImmutable('2021-01-01T00:00:00+00:00')
        );
        $refundableItemData2 = new RefundableItemData(
            'id2',
            YesNoBackedEnum::NO,
            null
        );

        $reflectionClass = new ReflectionClass($refundableItemData);
        $property = $reflectionClass->getProperty('refundableUntil');

        $dateTimeSerializer = new DateTimeSerializer();

        self::assertEquals(
            ['refundableUntil' => '2021-01-01T00:00:00+00:00'],
            $dateTimeSerializer->serialize(PropertyContext::getInstance($property), $refundableItemData)
        );

        self::assertEquals(
            ['refundableUntil' => null],
            $dateTimeSerializer->serialize(PropertyContext::getInstance($property), $refundableItemData2)
        );

        $serializedData = ['refundableUntil' => '2021-01-01T00:00:00+00:00'];

        self::assertEquals(
            new DateTimeImmutable('2021-01-01T00:00:00+00:00'),
            $dateTimeSerializer->deserialize(PropertyContext::getInstance($property), $serializedData)
        );

        self::assertEquals(
            null,
            $dateTimeSerializer->deserialize(PropertyContext::getInstance($property), ['refundableUntil' => null])
        );
    }

    /**
     * @throws Throwable
     */
    #[Test]
    public function will_throw_exception_when_property_is_not_nullable_and_it_fails_to_resolve_date_time_instance(): void
    {
        $data = [
            'id' => 'id',
            'birthdate' => null
        ];

        $data2 = [
            'id' => 'id',
            'birthdate' => 123
        ];

        $reflectionClass = new ReflectionClass(UserBirthdateData::class);
        $property = $reflectionClass->getProperty('birthdate');

        $dateTimeSerializer = new DateTimeSerializer();

        self::expectException(DeserializeException::class);
        $dateTimeSerializer->deserialize(PropertyContext::getInstance($property), $data);

        self::expectException(DeserializeException::class);
        $dateTimeSerializer->deserialize(PropertyContext::getInstance($property), $data2);
    }

    /**
     * @throws Throwable
     */
    #[Test]
    public function will_default_to_datetime_immutable_when_declaring_a_property_with_date_time_interface(): void
    {
        $userBirthdateArr = [
            'id' => 'id',
            'birthdate' => '2021-01-01T00:00:00+00:00'
        ];

        $reflectionClass = new ReflectionClass(UserBirthdateData::class);
        $property = $reflectionClass->getProperty('birthdate');

        self::assertEquals(
            new DateTimeImmutable('2021-01-01T00:00:00+00:00'),
            new DateTimeSerializer()->deserialize(PropertyContext::getInstance($property), $userBirthdateArr)
        );
    }
}
