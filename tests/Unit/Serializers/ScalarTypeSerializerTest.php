<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Serializers;

use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Enums\Property\Type;
use Nuxtifyts\PhpDto\Exceptions\DeserializeException;
use Nuxtifyts\PhpDto\Tests\Dummies\CoordinatesData;
use Nuxtifyts\PhpDto\Tests\Dummies\PersonData;
use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;
use Nuxtifyts\PhpDto\Serializers\Serializer;
use Nuxtifyts\PhpDto\Serializers\ScalarTypeSerializer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use ReflectionClass;
use Throwable;

#[CoversClass(ScalarTypeSerializer::class)]
#[CoversClass(Serializer::class)]
#[UsesClass(PropertyContext::class)]
#[UsesClass(PersonData::class)]
#[UsesClass(CoordinatesData::class)]
final class ScalarTypeSerializerTest extends UnitCase
{
    #[Test]
    public function supports_scalar_types(): void
    {
        self::assertEquals(
            [
                Type::INT,
                Type::FLOAT,
                Type::STRING,
                Type::BOOLEAN,
            ],
            ScalarTypeSerializer::supportedTypes()
        );
    }

    /**
     * @param array<string, mixed> $expectedSerializedValue
     *
     * @throws Throwable
     */
    #[Test]
    #[DataProvider('will_perform_data_serialization_on_scalar_types_data_provider')]
    public function will_perform_data_serialization_on_scalar_types_data(
        object $object,
        array $expectedSerializedValue,
        string $propertyName,
        mixed $expectedDeserializedValue
    ): void {
        $reflectionClass = new ReflectionClass($object);
        $property = $reflectionClass->getProperty($propertyName);

        $scalarTypeSerializer = new ScalarTypeSerializer();

        self::assertEquals(
            $expectedSerializedValue,
            $scalarTypeSerializer->serialize(
                PropertyContext::getInstance($property),
                $object
            )
        );

        self::assertEquals(
            $expectedDeserializedValue,
            $scalarTypeSerializer->deserialize(
                PropertyContext::getInstance($property),
                $expectedSerializedValue
            )
        );
    }

    /**
     * @throws Throwable
     */
    #[Test]
    public function will_throw_an_exception_if_it_fails_deserialize_value(): void
    {
        $object = new readonly class ('Hello') extends Data {
            public function __construct(
                public string $value
            ) {
            }
        };

        $reflectionClass = new ReflectionClass($object);
        $property = $reflectionClass->getProperty('value');

        $scalarTypeSerializer = new ScalarTypeSerializer();

        self::expectException(DeserializeException::class);

        $scalarTypeSerializer->deserialize(
            PropertyContext::getInstance($property),
            ['value' => null]
        );
    }

    /**
     * @throws Throwable
     */
    #[Test]
    public function will_support_nullable_properties(): void
    {
        $object = new readonly class (null) extends Data {
            public function __construct(
                public ?string $value
            ) {
            }
        };

        $reflectionClass = new ReflectionClass($object);
        $property = $reflectionClass->getProperty('value');

        $scalarTypeSerializer = new ScalarTypeSerializer();

        self::assertEquals(
            ['value' => null],
            $scalarTypeSerializer->serialize(
                PropertyContext::getInstance($property),
                $object
            )
        );

        self::assertEquals(
            null,
            $scalarTypeSerializer->deserialize(
                PropertyContext::getInstance($property),
                ['value' => null]
            )
        );
    }

    /**
     * @return array<string, array{
     *     object: mixed,
     *     expectedSerializedValue: array<string, mixed>,
     *     propertyName: string
     * }>
     */
    public static function will_perform_data_serialization_on_scalar_types_data_provider(): array
    {
        return [
            'Integer types' => [
                'object' => new readonly class (42) extends Data {
                    public function __construct(
                        public int $number
                    ) {
                    }
                },
                'expectedSerializedValue' => ['number' => 42],
                'propertyName' => 'number',
                'expectedDeserializedValue' => 42
            ],
            'Float types' => [
                'object' => new readonly class (3.14) extends Data {
                    public function __construct(
                        public float $number
                    ) {
                    }
                },
                'expectedSerializedValue' => ['number' => 3.14],
                'propertyName' => 'number',
                'expectedDeserializedValue' => 3.14
            ],
            'Boolean types' => [
                'object' => new readonly class (true) extends Data {
                    public function __construct(
                        public bool $value
                    ) {
                    }
                },
                'expectedSerializedValue' => ['value' => true],
                'propertyName' => 'value',
                'expectedDeserializedValue' => true
            ],
            'String types' => [
                'object' => new readonly class ('Hello, World!') extends Data {
                    public function __construct(
                        public string $message
                    ) {
                    }
                },
                'expectedSerializedValue' => ['message' => 'Hello, World!'],
                'propertyName' => 'message',
                'expectedDeserializedValue' => 'Hello, World!'
            ]
        ];
    }
}
