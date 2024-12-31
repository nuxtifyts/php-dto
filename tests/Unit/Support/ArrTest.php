<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Support;

use Nuxtifyts\PhpDto\Serializers\BackedEnumSerializer;
use Nuxtifyts\PhpDto\Serializers\ScalarTypeSerializer;
use Nuxtifyts\PhpDto\Serializers\Serializer;
use Nuxtifyts\PhpDto\Support\Arr;
use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(Arr::class)]
final class ArrTest extends UnitCase
{
    /**
     * @param array<string, mixed> $parameters
     */
    #[Test]
    #[DataProvider('get_arr_provider')]
    #[DataProvider('is_array_of_class_strings_provider')]
    public function arr_helper_functions(
        string $functionName,
        array $parameters,
        mixed $expected
    ): void {
        self::assertTrue(method_exists(Arr::class, $functionName));
        self::assertEquals(
            $expected,
            Arr::{$functionName}(...$parameters)
        );
    }

    /**
     * @return array<string, mixed>
     */
    public static function get_arr_provider(): array
    {
        return [
            'get array existing key, invalid value' => [
                'getArray',
                [
                    'array' => ['key' => 'value'],
                    'key' => 'key',
                ],
                [],
            ],
            'get array existing key, valid value' => [
                'getArray',
                [
                    'array' => ['key' => ['value']],
                    'key' => 'key',
                ],
                ['value'],
            ],
            'get array non-existing key' => [
                'getArray',
                [
                    'array' => ['key' => 'value'],
                    'key' => 'nonExistingKey',
                ],
                [],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function is_array_of_class_strings_provider(): array
    {
        return [
            'is array of class strings, valid' => [
                'isArrayOfClassStrings',
                [
                    'array' => [
                        ScalarTypeSerializer::class,
                        BackedEnumSerializer::class,
                    ],
                    'classString' => Serializer::class,
                ],
                true,
            ],
            'is array of class strings, invalid' => [
                'isArrayOfClassStrings',
                [
                    'array' => [
                        ScalarTypeSerializer::class,
                        BackedEnumSerializer::class,
                        'invalid',
                    ],
                    'classString' => Serializer::class,
                ],
                false,
            ],
        ];
    }
}
