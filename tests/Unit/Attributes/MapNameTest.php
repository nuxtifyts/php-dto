<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Attributes;

use Nuxtifyts\PhpDto\Tests\Dummies\PropertyNameMapperData;
use Nuxtifyts\PhpDto\Attributes\Class\MapName;
use Nuxtifyts\PhpDto\Contexts\ClassContext;
use Nuxtifyts\PhpDto\Contexts\ClassContext\NameMapperConfig;
use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Pipelines\DeserializePipeline\Pipes\MapNamesPipe;
use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\Test;
use Throwable;

#[CoversClass(MapName::class)]
#[CoversClass(NameMapperConfig::class)]
#[CoversClass(ClassContext::class)]
#[CoversClass(MapNamesPipe::class)]
#[UsesClass(PropertyNameMapperData::class)]
final class MapNameTest extends UnitCase
{
    /**
     * @param class-string<Data> $dataClassString
     * @param array<string, mixed> $data
     * @param array<string, mixed> $expected
     *
     * @throws Throwable
     */
    #[Test]
    #[DataProvider('property_name_mapper_data_provider')]
    public function will_be_able_to_map_properties(
        string $dataClassString,
        array $data,
        array $expected
    ): void {
        $data = $dataClassString::from($data);

        foreach ($expected as $key => $value) {
            self::assertObjectHasProperty($key, $data);
            self::assertEquals($value, $data->{$key});
        }
    }

    /**
     * @return array<string, mixed>
     */
    public static function property_name_mapper_data_provider(): array
    {
        return [
            'snake_case' => [
                'dataClassString' => PropertyNameMapperData::class,
                'data' => [ 'camel_case' => 'value' ],
                'expected' => [ 'camelCase' => 'value' ]
            ],
            'kebab_case' => [
                'dataClassString' => PropertyNameMapperData::class,
                'data' => [ 'camel-case' => 'value' ],
                'expected' => [ 'camelCase' => 'value' ]
            ],
            'pascal_case' => [
                'dataClassString' => PropertyNameMapperData::class,
                'data' => [ 'CamelCase' => 'value' ],
                'expected' => [ 'camelCase' => 'value' ]
            ],
            'no_change' => [
                'dataClassString' => PropertyNameMapperData::class,
                'data' => [ 'camelCase' => 'value' ],
                'expected' => [ 'camelCase' => 'value' ]
            ],
            'multiple_letter_cases_can_be_transformed' => [
                'dataClassString' => PropertyNameMapperData::class,
                'data' => [ 'CamelCase' => 'value', 'un_kNoWnCAsE' => 'anotherValue', 'another_snake_case' => 'value' ],
                'expected' => [ 'camelCase' => 'value' ]
            ]
        ];
    }
}
