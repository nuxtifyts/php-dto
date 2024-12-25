<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\DataRefiners;

use Nuxtifyts\PhpDto\Attributes\Property\WithRefiner;
use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\DataRefiners\DateTimeRefiner;
use Nuxtifyts\PhpDto\Exceptions\InvalidRefiner;
use Nuxtifyts\PhpDto\Pipelines\DeserializePipeline\RefineDataPipe;
use Nuxtifyts\PhpDto\Serializers\DateTimeSerializer;
use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use ReflectionProperty;
use DateTimeInterface;
use DateTimeImmutable;
use Throwable;

#[CoversClass(DateTimeRefiner::class)]
#[CoversClass(RefineDataPipe::class)]
#[CoversClass(InvalidRefiner::class)]
#[CoversClass(DateTimeSerializer::class)]
#[UsesClass(PropertyContext::class)]
final class DateTimeRefinerTest extends UnitCase
{
    /**
     * @param string|list<string>|null $formats
     *
     * @throws Throwable
     */
    #[Test]
    #[DataProvider('will_refine_a_string_to_a_dateTime_immutable_data_provider')]
    public function will_refine_a_string_to_a_datetime_immutable(
        object $object,
        string $propertyName,
        string|array|null $formats,
        mixed $value,
        mixed $expectedRefinedValue
    ): void {
        $propertyContext = PropertyContext::getInstance(
            new ReflectionProperty($object, $propertyName)
        );

        $refinedValue = new DateTimeRefiner($formats)->refine($value, $propertyContext);

        self::assertEquals(
            $expectedRefinedValue,
            $refinedValue instanceof DateTimeImmutable
                ? $refinedValue->format('Y-m-d H:i')
                : $refinedValue
        );
    }

    /**
     * @throws Throwable
     */
    #[Test]
    public function will_throw_an_exception_if_refiner_is_used_on_a_non_datetime_property(): void
    {
        $object = new readonly class ('2025-06-17') extends Data {
            public function __construct(
                #[WithRefiner(DateTimeRefiner::class)]
                public string $date
            ) {
            }
        };

        $propertyContext = PropertyContext::getInstance(
            new ReflectionProperty($object, 'date')
        );

        self::expectException(InvalidRefiner::class);
        new DateTimeRefiner()->refine('2025-06-17', $propertyContext);
    }

    /**
     * @return array<string, mixed>
     */
    public static function will_refine_a_string_to_a_dateTime_immutable_data_provider(): array
    {
        $now = new DateTimeImmutable();

        $object = new readonly class ($now) extends Data {
            public function __construct(
                #[WithRefiner(DateTimeRefiner::class)]
                public DateTimeImmutable $date
            ) {
            }
        };

        return [
            'Default formats Y-m-d' => [
                'object' => $object,
                'propertyName' => 'date',
                'formats' => null,
                'value' => $now->format('Y-m-d'),
                'expectedRefinedValue' => $now->format('Y-m-d H:i')
            ],
            'Default formats Y-m-d H:i:s' => [
                'object' => $object,
                'propertyName' => 'date',
                'formats' => null,
                'value' => $now->format('Y-m-d H:i:s'),
                'expectedRefinedValue' => $now->format('Y-m-d H:i')
            ],
            'Default formats ATOM' => [
                'object' => $object,
                'propertyName' => 'date',
                'formats' => null,
                'value' => $now->format(DateTimeInterface::ATOM),
                'expectedRefinedValue' => $now->format('Y-m-d H:i')
            ],
            'Ignores null' => [
                'object' => $object,
                'propertyName' => 'date',
                'formats' => null,
                'value' => null,
                'expectedRefinedValue' => null
            ],
            'Returns original value if not a string' => [
                'object' => $object,
                'propertyName' => 'date',
                'formats' => null,
                'value' => 123,
                'expectedRefinedValue' => 123
            ],
            'Returns original value if it fails to refine' => [
                'object' => $object,
                'propertyName' => 'date',
                'formats' => [DateTimeInterface::ATOM],
                'value' => $now->format('Y/m-d'),
                'expectedRefinedValue' => $now->format('Y/m-d')
            ]
        ];
    }

    /**
     * @throws Throwable
     */
    #[Test]
    public function will_refine_data_to_helper_dto_deserialization(): void
    {
        $object = new readonly class (null) extends Data {
            public function __construct(
                #[WithRefiner(DateTimeRefiner::class, formats: 'Y/m-d')]
                public ?DateTimeImmutable $date
            ) {
            }
        };

        $now = new DateTimeImmutable();

        $object2 = $object::from([
            'date' => $now->format('Y/m-d')
        ]);

        self::assertInstanceOf(DateTimeImmutable::class, $object2->date);
        self::assertEquals($now->format('Y-m-d'), $object2->date->format('Y-m-d'));
    }
}
