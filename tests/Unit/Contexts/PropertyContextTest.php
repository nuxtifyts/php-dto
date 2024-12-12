<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Contexts;

use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Enums\Property\Type;
use Nuxtifyts\PhpDto\Serializers\ScalarTypeSerializer;
use Nuxtifyts\PhpDto\Support\Traits\HasSerializers;
use Nuxtifyts\PhpDto\Tests\Dummies\Enums\YesNoBackedEnum;
use Nuxtifyts\PhpDto\Tests\Dummies\UnionMultipleTypeData;
use Nuxtifyts\PhpDto\Tests\Dummies\CoordinatesData;
use Nuxtifyts\PhpDto\Tests\Dummies\PersonData;
use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use DateTime;
use DateTimeInterface;
use DateTimeImmutable;
use ReflectionProperty;
use Throwable;

#[CoversClass(PropertyContext::class)]
#[CoversClass(HasSerializers::class)]
#[UsesClass(ScalarTypeSerializer::class)]
#[UsesClass(PersonData::class)]
#[UsesClass(Data::class)]
#[UsesClass(CoordinatesData::class)]
#[UsesClass(UnionMultipleTypeData::class)]
final class PropertyContextTest extends UnitCase
{
    /**
     * @throws Throwable
     */
    #[Test]
    public function can_create_an_instance_from_reflection_property(): void
    {
        $reflectionProperty = new ReflectionProperty(PersonData::class, 'firstName');
        $propertyContext = PropertyContext::getInstance($reflectionProperty);

        self::assertInstanceOf(PropertyContext::class, $propertyContext);
    }

    /**
     * @throws Throwable
     */
    #[Test]
    public function can_retrieve_same_instance_of_property(): void
    {
        $reflectionProperty = new ReflectionProperty(PersonData::class, 'firstName');
        $propertyContext = PropertyContext::getInstance($reflectionProperty);
        $propertyContext2 = PropertyContext::getInstance($reflectionProperty);

        self::assertSame($propertyContext, $propertyContext2);
    }

    /**
     * @throws Throwable
     */
    #[Test]
    public function can_resolve_serializers_of_property(): void
    {
        $reflectionProperty = new ReflectionProperty(PersonData::class, 'firstName');
        $propertyContext = PropertyContext::getInstance($reflectionProperty);

        $serializers = $propertyContext->serializers();

        self::assertCount(1, $serializers);
        self::assertInstanceOf(ScalarTypeSerializer::class, $serializers[0]);
    }

    /**
     * @throws Throwable
     */
    #[test]
    public function can_retrieve_property_value(): void
    {
        $reflectionProperty = new ReflectionProperty(PersonData::class, 'firstName');
        $propertyContext = PropertyContext::getInstance($reflectionProperty);

        $personData = new PersonData('John', 'Doe');

        self::assertEquals('John', $propertyContext->getValue($personData));
    }

    /**
     * @throws Throwable
     */
    #[Test]
    public function can_resolve_nullable_properties(): void
    {
        $reflectionProperty = new ReflectionProperty(CoordinatesData::class, 'radius');
        $propertyContext = PropertyContext::getInstance($reflectionProperty);

        self::assertTrue($propertyContext->isNullable);
    }

    /**
     * @throws Throwable
     */
    #[Test]
    public function can_resolve_union_types(): void
    {
        $reflectionProperty = new ReflectionProperty(UnionMultipleTypeData::class, 'value');
        $propertyContext = PropertyContext::getInstance($reflectionProperty);

        self::assertEquals(
            [
                Type::STRING,
                Type::INT,
                Type::FLOAT
            ],
            $propertyContext->types
        );
    }

    /**
     * @throws Throwable
     */
    #[Test]
    #[DataProvider('will_be_able_to_resolve_types_data_provider')]
    public function will_be_able_to_resolve_types(
        object $object,
        string $propertyName,
        Type $expectedType
    ): void {
        $reflectionProperty = new ReflectionProperty($object, $propertyName);
        $propertyContext = PropertyContext::getInstance($reflectionProperty);

        self::assertTrue(
            in_array(
                $expectedType,
                $propertyContext->types,
                true
            )
        );
    }

    /**
     * @return array<string, array{
     *   object: object,
     *   propertyName: string,
     *   expectedType: Type
     * }>
     */
    public static function will_be_able_to_resolve_types_data_provider(): array
    {
        return [
            'Resolves string' => [
                'object' => new readonly class ('John') extends Data {
                    public function __construct(
                        public string $value
                    ) {
                    }
                },
                'propertyName' => 'value',
                'expectedType' => Type::STRING
            ],
            'Resolves int' => [
                'object' => new readonly class (42) extends Data {
                    public function __construct(
                        public int $value
                    ) {
                    }
                },
                'propertyName' => 'value',
                'expectedType' => Type::INT
            ],
            'Resolves bool' => [
                'object' => new readonly class (true) extends Data {
                    public function __construct(
                        public bool $value
                    ) {
                    }
                },
                'propertyName' => 'value',
                'expectedType' => Type::BOOLEAN
            ],
            'Resolves float' => [
                'object' => new readonly class (3.14) extends Data {
                    public function __construct(
                        public float $value
                    ) {
                    }
                },
                'propertyName' => 'value',
                'expectedType' => Type::FLOAT
            ],
            'Resolves mixed' => [
                'object' => new readonly class (null) extends Data {
                    public function __construct(
                        public mixed $value
                    ) {
                    }
                },
                'propertyName' => 'value',
                'expectedType' => Type::MIXED
            ],
            'Resolves backed enum' => [
                'object' => new readonly class(YesNoBackedEnum::YES) extends Data {
                    public function __construct(
                        public YesNoBackedEnum $value
                    ) {
                    }
                },
                'propertyName' => 'value',
                'expectedType' => Type::BACKED_ENUM
            ],
            'Resolves datetime from date time' => [
                'object' => new readonly class(new DateTime()) extends Data {
                    public function __construct(
                        public DateTime $value
                    ) {
                    }
                },
                'propertyName' => 'value',
                'expectedType' => Type::DATETIME
            ],
            'Resolves datetime from date time immutable' => [
                'object' => new readonly class(new DateTimeImmutable()) extends Data {
                    public function __construct(
                        public DateTimeImmutable $value
                    ) {
                    }
                },
                'propertyName' => 'value',
                'expectedType' => Type::DATETIME
            ],
            'Resolves datetime from date time interface' => [
                'object' => new readonly class(new DateTimeImmutable()) extends Data {
                    public function __construct(
                        public DateTimeInterface $value
                    ) {
                    }
                },
                'propertyName' => 'value',
                'expectedType' => Type::DATETIME
            ]
        ];
    }
}
