<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Serializers;

use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\Enums\Property\Type;
use Nuxtifyts\PhpDto\Exceptions\DeserializeException;
use Nuxtifyts\PhpDto\Serializers\BackedEnumSerializer;
use Nuxtifyts\PhpDto\Serializers\Serializer;
use Nuxtifyts\PhpDto\Tests\Dummies\YesOrNoData;
use Nuxtifyts\PhpDto\Tests\Dummies\YesOrNoNullableData;
use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Nuxtifyts\PhpDto\Tests\Dummies\Enums\YesNoBackedEnum;
use ReflectionProperty;
use PHPUnit\Framework\Attributes\UsesClass;
use ReflectionClass;
use Throwable;

#[CoversClass(Serializer::class)]
#[CoversClass(BackedEnumSerializer::class)]
#[UsesClass(PropertyContext::class)]
#[UsesClass(YesOrNoData::class)]
#[UsesClass(YesNoBackedEnum::class)]
#[UsesClass(YesOrNoNullableData::class)]
final class BackedEnumSerializerTest extends UnitCase
{
    /**
     * @throws Throwable
     */
    #[Test]
    public function supports_backed_enum_type(): void
    {
        self::assertEquals(
            [
                Type::BACKED_ENUM
            ],
            BackedEnumSerializer::supportedTypes()
        );
    }

    /**
     * @throws Throwable
     */
    #[Test]
    public function will_perform_data_serialization_on_backed_enum_types_data(): void
    {
        $yesNoData = new YesOrNoData(YesNoBackedEnum::YES);

        $reflectionClass = new ReflectionClass($yesNoData);
        $property = $reflectionClass->getProperty('yesNo');

        $backedEnumSerializer = new BackedEnumSerializer();

        self::assertEquals(
            ['yesNo' => 'yes'],
            $backedEnumSerializer->serialize(PropertyContext::getInstance($property), $yesNoData)
        );

        $serializedData = ['yesNo' => 'yes'];

        self::assertEquals(
            YesNoBackedEnum::YES,
            $backedEnumSerializer->deserialize(PropertyContext::getInstance($property), $serializedData)
        );
    }

    /**
     * @throws Throwable
     */
    #[Test]
    public function will_throw_exception_when_property_is_not_nullable_and_it_fails_to_resolve_backed_enum_instance(): void
    {
        $property = new ReflectionProperty(YesOrNoData::class, 'yesNo');

        $backedEnumSerializer = new BackedEnumSerializer();

        self::expectException(DeserializeException::class);

        $backedEnumSerializer->deserialize(PropertyContext::getInstance($property), ['yesNo' => 'maybe']);
    }

    /**
     * @throws Throwable
     */
    #[Test]
    public function will_resolve_null_if_property_is_nullable(): void
    {
        $yesNoNullableData = new YesOrNoNullableData(null);

        $reflectionClass = new ReflectionClass($yesNoNullableData);
        $property = $reflectionClass->getProperty('yesNo');

        $backedEnumSerializer = new BackedEnumSerializer();

        self::assertEquals(
            ['yesNo' => null],
            $backedEnumSerializer->serialize(PropertyContext::getInstance($property), $yesNoNullableData)
        );

        $serializedData = ['yesNo' => null];

        self::assertNull(
            $backedEnumSerializer->deserialize(PropertyContext::getInstance($property), $serializedData)
        );
    }
}
