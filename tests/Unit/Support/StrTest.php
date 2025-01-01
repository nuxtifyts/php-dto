<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Support;

use Nuxtifyts\PhpDto\Enums\LetterCase;
use Nuxtifyts\PhpDto\Support\Str;
use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(Str::class)]
final class StrTest extends UnitCase
{
    #[Test]
    #[DataProvider('camel_case_provider')]
    public function is_camel_case(string $value, bool $expected): void
    {
        $this->assertSame($expected, Str::isCamelCase($value));
    }

    #[Test]
    #[DataProvider('snake_case_provider')]
    public function is_snake_case(string $value, bool $expected): void
    {
        $this->assertSame($expected, Str::isSnakeCase($value));
    }

    #[Test]
    #[DataProvider('kebab_case_provider')]
    public function is_kebab_case(string $value, bool $expected): void
    {
        $this->assertSame($expected, Str::isKebabCase($value));
    }

    #[Test]
    #[DataProvider('pascal_case_provider')]
    public function is_pascal_case(string $value, bool $expected): void
    {
        $this->assertSame($expected, Str::isPascalCase($value));
    }

    #[Test]
    #[DataProvider('transform_provider')]
    public function transform_letter_case(string $value, LetterCase $from, LetterCase $to, string $expected): void
    {
        $this->assertSame($expected, Str::transformLetterCase($value, $from, $to));
    }

    #[Test]
    #[DataProvider('validate_letter_case_provider')]
    public function validate_letter_case(string $value, LetterCase $letterCase, bool $expected): void
    {
        $this->assertSame($expected, Str::validateLetterCase($value, $letterCase));
    }

    /**
     * @return array<string, mixed>
     */
    public static function camel_case_provider(): array
    {
        return [
            'test_is_camel_case_with_camelCase' => ['camelCase', true],
            'test_is_camel_case_with_CamelCase' => ['CamelCase', false],
            'test_is_camel_case_with_camel_case' => ['camel_case', false],
            'test_is_camel_case_with_camel-case' => ['camel-case', false],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function snake_case_provider(): array
    {
        return [
            'test_is_snake_case_with_snake_case' => ['snake_case', true],
            'test_is_snake_case_with_Snake_Case' => ['Snake_Case', false],
            'test_is_snake_case_with_snakeCase' => ['snakeCase', false],
            'test_is_snake_case_with_snake-case' => ['snake-case', false],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function kebab_case_provider(): array
    {
        return [
            'test_is_kebab_case_with_kebab-case' => ['kebab-case', true],
            'test_is_kebab_case_with_Kebab-Case' => ['Kebab-Case', false],
            'test_is_kebab_case_with_kebab_case' => ['kebab_case', false],
            'test_is_kebab_case_with_kebabCase' => ['kebabCase', false],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function pascal_case_provider(): array
    {
        return [
            'test_is_pascal_case_with_PascalCase' => ['PascalCase', true],
            'test_is_pascal_case_with_pascalCase' => ['pascalCase', false],
            'test_is_pascal_case_with_pascal_case' => ['pascal_case', false],
            'test_is_pascal_case_with_pascal-case' => ['pascal-case', false],
        ];
    }

    /** 
     * @return array<string, mixed> 
     */
    public static function transform_provider(): array
    {
        return [
            'test_transform_letter_case_with_camelCase_to_camelCase' => ['camelCase', LetterCase::CAMEL, LetterCase::CAMEL, 'camelCase'],
            'test_transform_letter_case_with_camelCase_to_snake_case' => ['camelCase', LetterCase::CAMEL, LetterCase::SNAKE, 'camel_case'],
            'test_transform_letter_case_with_camel_case_to_camelCase' => ['camel_case', LetterCase::SNAKE, LetterCase::CAMEL, 'camelCase'],
            'test_transform_letter_case_with_kebab_case_to_camelCase' => ['kebab-case', LetterCase::KEBAB, LetterCase::CAMEL, 'kebabCase'],
            'test_transform_letter_case_with_PascalCase_to_snake_case' => ['PascalCase', LetterCase::PASCAL, LetterCase::SNAKE, 'pascal_case'],
            'test_transform_letter_case_with_snake_case_to_kebab_case' => ['snake_case', LetterCase::SNAKE, LetterCase::KEBAB, 'snake-case'],
            'test_transform_letter_case_with_kebab_case_to_PascalCase' => ['kebab-case', LetterCase::KEBAB, LetterCase::PASCAL, 'KebabCase'],
            'test_transform_letter_case_with_camelCase_to_CamelCase' => ['camelCase', LetterCase::CAMEL, LetterCase::PASCAL, 'CamelCase'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function validate_letter_case_provider(): array
    {
        return [
            'test_validate_letter_case_with_camelCase' => ['camelCase', LetterCase::CAMEL, true],
            'test_validate_letter_case_with_snake_case' => ['snake_case', LetterCase::SNAKE, true],
            'test_validate_letter_case_with_kebab-case' => ['kebab-case', LetterCase::KEBAB, true],
            'test_validate_letter_case_with_PascalCase' => ['PascalCase', LetterCase::PASCAL, true],
            'test_validate_letter_case_with_invalid_camelCase' => ['CamelCase', LetterCase::CAMEL, false],
            'test_validate_letter_case_with_invalid_snake_case' => ['SnakeCase', LetterCase::SNAKE, false],
            'test_validate_letter_case_with_invalid_kebab-case' => ['KebabCase', LetterCase::KEBAB, false],
            'test_validate_letter_case_with_invalid_PascalCase' => ['pascalCase', LetterCase::PASCAL, false],
        ];
    }
}
