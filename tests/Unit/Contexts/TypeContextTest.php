<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Contexts;

use DateTime;
use DateTimeImmutable;
use Nuxtifyts\PhpDto\Attributes\PropertyAttributes\Types\ArrayOfBackedEnums;
use Nuxtifyts\PhpDto\Attributes\PropertyAttributes\Types\ArrayOfData;
use Nuxtifyts\PhpDto\Attributes\PropertyAttributes\Types\ArrayOfDateTimes;
use Nuxtifyts\PhpDto\Attributes\PropertyAttributes\Types\ArrayOfScalarTypes;
use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\Contexts\TypeContext;
use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Enums\Property\Type;
use Nuxtifyts\PhpDto\Tests\Dummies\ArrayOfAttributesData;
use Nuxtifyts\PhpDto\Tests\Dummies\Enums\ColorsBackedEnum;
use Nuxtifyts\PhpDto\Tests\Dummies\Enums\YesNoBackedEnum;
use Nuxtifyts\PhpDto\Tests\Dummies\PersonData;
use Nuxtifyts\PhpDto\Tests\Dummies\UserData;
use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use ReflectionClass;
use ReflectionProperty;
use Throwable;

#[CoversClass(PropertyContext::class)]
#[CoversClass(TypeContext::class)]
#[CoversClass(ArrayOfScalarTypes::class)]
#[CoversClass(ArrayOfBackedEnums::class)]
#[CoversClass(ArrayOfDateTimes::class)]
#[CoversClass(ArrayOfData::class)]
#[UsesClass(ArrayOfAttributesData::class)]
#[UsesClass(UserData::class)]
#[UsesClass(PersonData::class)]
final class TypeContextTest extends UnitCase
{
    /**
     * @param list<Type> $expectedSubTypes,
     * @param list<ReflectionClass<Data>> $expectedReflectionClasses
     *
     * @throws Throwable
     */
    #[Test]
    #[DataProvider('will_be_able_to_resolve_array_types_provider')]
    public function will_be_able_to_resolve_array_types(
        Data $object,
        string $propertyName,
        array $expectedSubTypes,
        array $expectedReflectionClasses
    ): void {
        $propertyContext = PropertyContext::getInstance(
            new ReflectionProperty($object::class, $propertyName),
        );

        self::assertInstanceOf(
            TypeContext::class,
            $arrayTypeContext = array_find(
                $propertyContext->typeContexts,
                static fn (TypeContext $typeContext) => $typeContext->type === Type::ARRAY
            )
        );

        /** @var TypeContext<Type::ARRAY> $arrayTypeContext */

        self::assertEquals(
            $expectedSubTypes,
            $arrayTypeContext->arrayElementTypes
        );

        if (!empty($expectedReflectionClasses)) {
            self::assertEmpty(
                array_diff(
                    $expectedReflectionClasses,
                    array_map(
                        static fn (TypeContext $typeContext) => $typeContext->reflection?->getName() ?? '',
                        $arrayTypeContext->subTypeContexts ?? []
                    )
                )
            );
        }
    }

    /**
     * @return array<string, array{
     *     object: Data,
     *     propertyName: string,
     *     expectedSubTypes: list<Type>,
     *     expectedReflectionClasses: list<class-string<object>>
     * }>
     */
    public static function will_be_able_to_resolve_array_types_provider(): array
    {
        return [
            'Resolves array of ints' => [
                'object' => new readonly class([1, 2]) extends Data {
                    /**
                     * @param list<int> $value
                     */
                    public function __construct(
                        #[ArrayOfScalarTypes(Type::INT)]
                        public array $value,
                    ) {
                    }
                },
                'propertyName' => 'value',
                'expectedSubTypes' => [
                    Type::INT
                ],
                'expectedReflectionClasses' => []
            ],
            'Resolves array of ints and floats' => [
                'object' => new readonly class([1, 2.0]) extends Data {
                    /**
                     * @param list<int|float> $value
                     */
                    public function __construct(
                        #[ArrayOfScalarTypes([Type::INT, Type::FLOAT])]
                        public array $value,
                    ) {
                    }
                },
                'propertyName' => 'value',
                'expectedSubTypes' => [
                    Type::INT,
                    Type::FLOAT
                ],
                'expectedReflectionClasses' => []
            ],
            'Resolves array of ints and floats and strings using one attribute' => [
                'object' => new readonly class([1, 2.0, '3']) extends Data {
                    /**
                     * @param list<int|float|string> $value
                     */
                    public function __construct(
                        #[ArrayOfScalarTypes([Type::INT, Type::FLOAT, Type::STRING])]
                        public array $value,
                    ) {
                    }
                },
                'propertyName' => 'value',
                'expectedSubTypes' => [
                    Type::INT,
                    Type::FLOAT,
                    Type::STRING
                ],
                'expectedReflectionClasses' => []
            ],
            'Resolves array of ints and floats and strings using multiple attributes' => [
                'object' => new readonly class([1, 2.0, '3']) extends Data {
                    /**
                     * @param list<int|float|string> $value
                     */
                    public function __construct(
                        #[ArrayOfScalarTypes(Type::INT)]
                        #[ArrayOfScalarTypes(Type::FLOAT)]
                        #[ArrayOfScalarTypes(Type::STRING)]
                        public array $value,
                    ) {
                    }
                },
                'propertyName' => 'value',
                'expectedSubTypes' => [
                    Type::INT,
                    Type::FLOAT,
                    Type::STRING
                ],
                'expectedReflectionClasses' => []
            ],
            'Resolves array of backed enums' => [
                'object' => new readonly class([YesNoBackedEnum::YES, YesNoBackedEnum::NO]) extends Data {
                    /**
                     * @param list<YesNoBackedEnum> $value
                     */
                    public function __construct(
                        #[ArrayOfBackedEnums(YesNoBackedEnum::class)]
                        public array $value,
                    ) {
                    }
                },
                'propertyName' => 'value',
                'expectedSubTypes' => [
                    Type::BACKED_ENUM
                ],
                'expectedReflectionClasses' => [
                    YesNoBackedEnum::class,
                ]
            ],
            'Resolves array of backed enums multiple ones' => [
                'object' => new readonly class([ColorsBackedEnum::RED]) extends Data {
                    /**
                     * @param list<ColorsBackedEnum|YesNoBackedEnum> $value
                     */
                    public function __construct(
                        #[ArrayOfBackedEnums([ColorsBackedEnum::class, YesNoBackedEnum::class])]
                        public array $value,
                    ) {
                    }
                },
                'propertyName' => 'value',
                'expectedSubTypes' => [
                    Type::BACKED_ENUM,
                    Type::BACKED_ENUM
                ],
                'expectedReflectionClasses' => [
                    ColorsBackedEnum::class,
                    YesNoBackedEnum::class,
                ]
            ],
            'Resolves array of backed enums multiple once using multiple attributes' => [
                'object' => new readonly class([ColorsBackedEnum::RED]) extends Data {
                    /**
                     * @param list<ColorsBackedEnum|YesNoBackedEnum> $value
                     */
                    public function __construct(
                        #[ArrayOfBackedEnums(ColorsBackedEnum::class)]
                        #[ArrayOfBackedEnums(YesNoBackedEnum::class)]
                        public array $value,
                    ) {
                    }
                },
                'propertyName' => 'value',
                'expectedSubTypes' => [
                    Type::BACKED_ENUM,
                    Type::BACKED_ENUM
                ],
                'expectedReflectionClasses' => [
                    ColorsBackedEnum::class,
                    YesNoBackedEnum::class,
                ]
            ],
            'Resolves array of date times once' => [
                'object' => new readonly class([new DateTime()]) extends Data {
                    /**
                     * @param list<DateTime> $value
                     */
                    public function __construct(
                        #[ArrayOfDateTimes(DateTime::class)]
                        public array $value,
                    ) {
                    }
                },
                'propertyName' => 'value',
                'expectedSubTypes' => [
                    Type::DATETIME
                ],
                'expectedReflectionClasses' => [
                    DateTime::class
                ]
            ],
            'Resolves array of date times many time using one attribute' => [
                'object' => new readonly class([new DateTime(), new DateTimeImmutable()]) extends Data {
                    /**
                     * @param list<DateTime|DateTimeImmutable> $value
                     */
                    public function __construct(
                        #[ArrayOfDateTimes([DateTime::class, DateTimeImmutable::class])]
                        public array $value,
                    ) {
                    }
                },
                'propertyName' => 'value',
                'expectedSubTypes' => [
                    Type::DATETIME,
                    Type::DATETIME
                ],
                'expectedReflectionClasses' => [
                    DateTime::class,
                    DateTimeImmutable::class
                ]
            ],
            'Resolves array of date times many time using multiple attributes' => [
                'object' => new readonly class([new DateTime(), new DateTimeImmutable()]) extends Data {
                    /**
                     * @param list<DateTime|DateTimeImmutable> $value
                     */
                    public function __construct(
                        #[ArrayOfDateTimes(DateTime::class)]
                        #[ArrayOfDateTimes(DateTimeImmutable::class)]
                        public array $value,
                    ) {
                    }
                },
                'propertyName' => 'value',
                'expectedSubTypes' => [
                    Type::DATETIME,
                    Type::DATETIME
                ],
                'expectedReflectionClasses' => [
                    DateTime::class,
                    DateTimeImmutable::class
                ]
            ],
            'resolves array of data once' => [
                'object' => new readonly class([new UserData(firstName: 'John', lastName: 'Doe')]) extends Data {
                    /**
                     * @param list<UserData> $value
                     */
                    public function __construct(
                        #[ArrayOfData(UserData::class)]
                        public array $value,
                    ) {
                    }
                },
                'propertyName' => 'value',
                'expectedSubTypes' => [
                    Type::DATA
                ],
                'expectedReflectionClasses' => [
                    UserData::class
                ]
            ],
            'resolves array of many data classes using one attribute' => [
                'object' => new readonly class([new UserData(firstName: 'John', lastName: 'Doe'), new PersonData(firstName: 'Jane', lastName: 'Doe')]) extends Data {
                    /**
                     * @param list<UserData|PersonData> $value
                     */
                    public function __construct(
                        #[ArrayOfData([UserData::class, PersonData::class])]
                        public array $value,
                    ) {
                    }
                },
                'propertyName' => 'value',
                'expectedSubTypes' => [
                    Type::DATA,
                    Type::DATA
                ],
                'expectedReflectionClasses' => [
                    UserData::class,
                    PersonData::class
                ]
            ],
            'resolves array of many data classes using multiple attributes' => [
                'object' => new readonly class([new UserData(firstName: 'John', lastName: 'Doe'), new PersonData(firstName: 'Jane', lastName: 'Doe')]) extends Data {
                    /**
                     * @param list<UserData|PersonData> $value
                     */
                    public function __construct(
                        #[ArrayOfData(UserData::class)]
                        #[ArrayOfData(PersonData::class)]
                        public array $value,
                    ) {
                    }
                },
                'propertyName' => 'value',
                'expectedSubTypes' => [
                    Type::DATA,
                    Type::DATA
                ],
                'expectedReflectionClasses' => [
                    UserData::class,
                    PersonData::class
                ]
            ],
        ];
    }
}
