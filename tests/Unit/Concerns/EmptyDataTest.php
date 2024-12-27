<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Concerns;

use Nuxtifyts\PhpDto\Attributes\Property\Types\ArrayOfScalarTypes;
use Nuxtifyts\PhpDto\Contexts\ClassContext;
use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\Contracts\EmptyData as EmptyDataContract;
use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Enums\Property\Type;
use Nuxtifyts\PhpDto\Tests\Dummies\AddressData;
use Nuxtifyts\PhpDto\Tests\Dummies\CountryData;
use Nuxtifyts\PhpDto\Tests\Dummies\Enums\YesNoBackedEnum;
use Nuxtifyts\PhpDto\Tests\Dummies\PointData;
use Nuxtifyts\PhpDto\Tests\Dummies\PointGroupData;
use Nuxtifyts\PhpDto\Tests\Dummies\YesOrNoData;
use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Throwable;

#[CoversClass(Data::class)]
#[CoversClass(PropertyContext::class)]
#[CoversClass(ClassContext::class)]
#[UsesClass(ArrayOfScalarTypes::class)]
#[UsesClass(PointGroupData::class)]
#[UsesClass(PointData::class)]
#[UsesClass(AddressData::class)]
#[UsesClass(CountryData::class)]
#[UsesClass(YesOrNoData::class)]
final class EmptyDataTest extends UnitCase
{
    /**
     * @param class-string<EmptyDataContract>|EmptyDataContract $object
     * @param array<string, mixed> $expectedEmptyValues
     *
     * @throws Throwable
     */
    #[Test]
    #[DataProvider('will_be_able_to_create_empty_data_provider')]
    public function will_be_able_to_create_empty_data(
        EmptyDataContract|string $object,
        array $expectedEmptyValues
    ): void {
        $emptyData = $object::empty();

        foreach ($expectedEmptyValues as $property => $value) {
            self::assertObjectHasProperty($property, $emptyData);

            if ($value instanceof DateTimeInterface) {
                self::assertInstanceOf($value::class, $emptyData->{$property});
                self::assertEquals(
                    $value->format('Y-m-d H:i'),
                    $emptyData->{$property}->format('Y-m-d H:i')
                );
            } else {
                self::assertEquals($value, $emptyData->{$property});
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    public static function will_be_able_to_create_empty_data_provider(): array
    {
        return [
            'Will default to null if property is nullable' => [
                'object' => new readonly class ('') extends Data {
                    public function __construct(
                        public ?string $value
                    ) {
                    }
                },
                'expectedEmptyValues' => [
                    'value' => null
                ]
            ],
            'Will be able to create empty data with scalar types' => [
                'object' => new readonly class ('', 0, 0.0, false, null) extends Data {
                    public function __construct(
                        public string $value,
                        public int $number,
                        public float $float,
                        public bool $bool,
                        public ?string $nullableString = null
                    ) {
                    }
                },
                'expectedEmptyValues' => [
                    'value' => '',
                    'number' => 0,
                    'float' => 0.0,
                    'bool' => false,
                    'nullableString' => null
                ]
            ],
            'Will be able to create empty data with array types' => [
                'object' => new readonly class([], []) extends Data {
                    /**
                     * @param array<array-key, int|float> $arrayOfIntegersOrFloats
                     * @param ?array<array-key, int> $nullableArrayOfIntegers
                     */
                    public function __construct(
                        #[ArrayOfScalarTypes([Type::INT, Type::FLOAT])]
                        public array $arrayOfIntegersOrFloats,
                        #[ArrayOfScalarTypes([Type::INT])]
                        public ?array $nullableArrayOfIntegers
                    ) {
                    }
                },
                'expectedEmptyValues' => [
                    'arrayOfIntegersOrFloats' => [],
                    'nullableArrayOfIntegers' => null
                ]
            ],
            'Will be able to create empty data with data types array' => [
                'object' => PointGroupData::class,
                'expectedEmptyValues' => [
                    'key' => '',
                    'points' => []
                ]
            ],
            'Will be able to create empty data with data types direct data type' => [
                'object' => AddressData::class,
                'expectedEmptyValues' => [
                    'street' => '',
                    'city' => '',
                    'state' => '',
                    'zip' => '',
                    'country' => new CountryData('', ''),
                    'coordinates' => null
                ]
            ],
            'will take first value of backed enum when calling empty' => [
                'object' => YesOrNoData::class,
                'expectedEmptyValues' => [
                    'yesNo' => YesNoBackedEnum::YES,
                ]
            ],
            'Will default to a new instance for date time type' => [
                'object' => new readonly class (new DateTime()) extends Data {
                    public function __construct(
                        public DateTime $dateTime
                    ) {
                    }
                },
                'expectedEmptyValues' => [
                    'dateTime' => new DateTime()
                ]
            ],
            'Will default to a new instance for date time immutable type' => [
                'object' => new readonly class (new DateTimeImmutable()) extends Data {
                    public function __construct(
                        public DateTimeImmutable $dateTimeImmutable
                    ) {
                    }
                },
                'expectedEmptyValues' => [
                    'dateTimeImmutable' => new DateTimeImmutable()
                ]
            ],
        ];
    }
}
