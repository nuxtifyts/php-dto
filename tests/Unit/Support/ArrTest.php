<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Support;

use InvalidArgumentException;
use Nuxtifyts\PhpDto\Support\Arr;
use PHPUnit\Framework\Attributes\Test;
use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;
use PHPUnit\Framework\Attributes\UsesClass;
use Nuxtifyts\PhpDto\Serializers\Serializer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Nuxtifyts\PhpDto\Serializers\BackedEnumSerializer;
use Nuxtifyts\PhpDto\Serializers\ScalarTypeSerializer;
use Nuxtifyts\PhpDto\Tests\Dummies\Enums\YesNoBackedEnum;
use Nuxtifyts\PhpDto\Tests\Dummies\Enums\ColorsBackedEnum;

#[CoversClass(Arr::class)]
#[UsesClass(YesNoBackedEnum::class)]
#[UsesClass(ColorsBackedEnum::class)]
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
            'get string existing key, invalid value' => [
                'getString',
                [
                    'array' => ['key' => 1],
                    'key' => 'key',
                ],
                '',
            ],
            'get string existing key, valid value' => [
                'getString',
                [
                    'array' => ['key' => 'value'],
                    'key' => 'key',
                ],
                'value',
            ],
            'get string non-existing key' => [
                'getString',
                [
                    'array' => ['key' => 'value'],
                    'key' => 'nonExistingKey',
                ],
                '',
            ],
            'get string or null existing key, invalid value' => [
                'getStringOrNull',
                [
                    'array' => ['key' => 1],
                    'key' => 'key',
                ],
                null,
            ],
            'get string or null existing key, valid value' => [
                'getStringOrNull',
                [
                    'array' => ['key' => 'value'],
                    'key' => 'key',
                ],
                'value',
            ],
            'get string or null non-existing key' => [
                'getStringOrNull',
                [
                    'array' => ['key' => 'value'],
                    'key' => 'nonExistingKey',
                ],
                null,
            ],
            'get integer existing key, invalid value' => [
                'getInteger',
                [
                    'array' => ['key' => 'value'],
                    'key' => 'key',
                ],
                0,
            ],
            'get integer existing key, valid value' => [
                'getInteger',
                [
                    'array' => ['key' => 1],
                    'key' => 'key',
                ],
                1,
            ],
            'get integer non-existing key' => [
                'getInteger',
                [
                    'array' => ['key' => 1],
                    'key' => 'nonExistingKey',
                ],
                0,
            ],
            'get integer or null existing key, invalid value' => [
                'getIntegerOrNull',
                [
                    'array' => ['key' => 'value'],
                    'key' => 'key',
                ],
                null,
            ],
            'get integer or null existing key, valid value' => [
                'getIntegerOrNull',
                [
                    'array' => ['key' => 1],
                    'key' => 'key',
                ],
                1,
            ],
            'get integer or null non-existing key' => [
                'getIntegerOrNull',
                [
                    'array' => ['key' => 1],
                    'key' => 'nonExistingKey',
                ],
                null,
            ],
            'get float existing key, invalid value' => [
                'getFloat',
                [
                    'array' => ['key' => 'value'],
                    'key' => 'key',
                ],
                0.0,
            ],
            'get float existing key, valid value' => [
                'getFloat',
                [
                    'array' => ['key' => 1.1],
                    'key' => 'key',
                ],
                1.1,
            ],
            'get float non-existing key' => [
                'getFloat',
                [
                    'array' => ['key' => 1.1],
                    'key' => 'nonExistingKey',
                ],
                0.0,
            ],
            'get float or null existing key, invalid value' => [
                'getFloatOrNull',
                [
                    'array' => ['key' => 'value'],
                    'key' => 'key',
                ],
                null,
            ],
            'get float or null existing key, valid value' => [
                'getFloatOrNull',
                [
                    'array' => ['key' => 1.1],
                    'key' => 'key',
                ],
                1.1,
            ],
            'get float or null non-existing key' => [
                'getFloatOrNull',
                [
                    'array' => ['key' => 1.1],
                    'key' => 'nonExistingKey',
                ],
                null,
            ],
            'get boolean existing key, invalid value' => [
                'getBoolean',
                [
                    'array' => ['key' => 'value'],
                    'key' => 'key',
                ],
                false,
            ],
            'get boolean existing key, valid value' => [
                'getBoolean',
                [
                    'array' => ['key' => true],
                    'key' => 'key',
                ],
                true,
            ],
            'get boolean non-existing key' => [
                'getBoolean',
                [
                    'array' => ['key' => true],
                    'key' => 'nonExistingKey',
                ],
                false,
            ],
            'get boolean or null existing key, invalid value' => [
                'getBooleanOrNull',
                [
                    'array' => ['key' => 'value'],
                    'key' => 'key',
                ],
                null,
            ],
            'get boolean or null existing key, valid value' => [
                'getBooleanOrNull',
                [
                    'array' => ['key' => true],
                    'key' => 'key',
                ],
                true,
            ],
            'get boolean or null non-existing key' => [
                'getBooleanOrNull',
                [
                    'array' => ['key' => true],
                    'key' => 'nonExistingKey',
                ],
                null,
            ],
            'get backed enum or null, invalid value' => [
                'getBackedEnumOrNull',
                [
                    'array' => ['key' => 'invalid'],
                    'key' => 'key',
                    'enumClass' => YesNoBackedEnum::class,
                ],
                null
            ],
            'get backed enum or null, invalid value default provided' => [
                'getBackedEnumOrNull',
                [
                    'array' => ['key' => 'invalid'],
                    'key' => 'key',
                    'enumClass' => YesNoBackedEnum::class,
                    'default' => YesNoBackedEnum::NO,
                ],
                YesNoBackedEnum::NO
            ],
            'get backed enum or null, valid backed enum value' => [
                'getBackedEnumOrNull',
                [
                    'array' => ['key' => YesNoBackedEnum::YES],
                    'key' => 'key',
                    'enumClass' => YesNoBackedEnum::class,
                ],
                YesNoBackedEnum::YES
            ],
            'get backed enum or null, valid string value' => [
                'getBackedEnumOrNull',
                [
                    'array' => ['key' => 'yes'],
                    'key' => 'key',
                    'enumClass' => YesNoBackedEnum::class,
                ],
                YesNoBackedEnum::YES
            ],
            'get backed enum, invalid value' => [
                'getBackedEnum',
                [
                    'array' => ['key' => 'invalid'],
                    'key' => 'key',
                    'enumClass' => YesNoBackedEnum::class,
                    'default' => YesNoBackedEnum::NO,
                ],
                YesNoBackedEnum::NO
            ],
            'get backed enum, valid backed enum value' => [
                'getBackedEnum',
                [
                    'array' => ['key' => YesNoBackedEnum::YES],
                    'key' => 'key',
                    'enumClass' => YesNoBackedEnum::class,
                    'default' => YesNoBackedEnum::NO,
                ],
                YesNoBackedEnum::YES
            ],
            'get backed enum, valid string value' => [
                'getBackedEnum',
                [
                    'array' => ['key' => 'yes'],
                    'key' => 'key',
                    'enumClass' => YesNoBackedEnum::class,
                    'default' => YesNoBackedEnum::NO,
                ],
                YesNoBackedEnum::YES
            ],
        ];
    }

    #[Test]
    public function get_backed_enum_or_null_will_throw_an_exception_if_default_value_is_invalid(): void
    {
        self::expectException(InvalidArgumentException::class);

        Arr::getBackedEnumOrNull(
            ['key' => 'invalid'],
            'key',
            YesNoBackedEnum::class,
            ColorsBackedEnum::RED
        );
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
