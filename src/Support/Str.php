<?php

namespace Nuxtifyts\PhpDto\Support;

use Nuxtifyts\PhpDto\Enums\LetterCase;

final readonly class Str
{
    public static function validateLetterCase(string $value, LetterCase $letterCase): bool
    {
        return match ($letterCase) {
            LetterCase::CAMEL => self::isCamelCase($value),
            LetterCase::SNAKE => self::isSnakeCase($value),
            LetterCase::KEBAB => self::isKebabCase($value),
            LetterCase::PASCAL => self::isPascalCase($value),
        };
    }

    public static function isCamelCase(string $value): bool
    {
        return preg_match('/^[a-z]+(?:[A-Z][a-z]+)*$/', $value) === 1;
    }

    public static function isSnakeCase(string $value): bool
    {
        return preg_match('/^[a-z]+(?:_[a-z]+)*$/', $value) === 1;
    }

    public static function isKebabCase(string $value): bool
    {
        return preg_match('/^[a-z]+(?:-[a-z]+)*$/', $value) === 1;
    }

    public static function isPascalCase(string $value): bool
    {
        return preg_match('/^[A-Z][a-z]+(?:[A-Z][a-z]+)*$/', $value) === 1;
    }

    public static function transformLetterCase(
        string $value,
        LetterCase $from,
        LetterCase $to
    ): string {
        if ($from === $to) {
            return $value;
        }

        $value = match ($from) {
            LetterCase::CAMEL => self::camelToSnake($value),
            LetterCase::SNAKE => self::snakeToCamel($value),
            LetterCase::KEBAB => self::kebabToCamel($value),
            LetterCase::PASCAL => self::pascalToSnake($value),
        };

        return match ($to) {
            LetterCase::CAMEL => self::snakeToCamel($value),
            LetterCase::SNAKE => $value,
            LetterCase::KEBAB => self::camelToKebab($value),
            LetterCase::PASCAL => self::snakeToPascal($value),
        };
    }

    public static function camelToSnake(string $value): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $value) ?? '');
    }

    public static function snakeToCamel(string $value): string
    {
        return lcfirst(str_replace('_', '', ucwords($value, '_')));
    }

    public static function kebabToCamel(string $value): string
    {
        return lcfirst(str_replace('-', '', ucwords($value, '-')));
    }

    public static function pascalToSnake(string $value): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $value) ?? '');
    }

    public static function snakeToPascal(string $value): string
    {
        return str_replace('_', '', ucwords($value, '_'));
    }

    public static function camelToKebab(string $value): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $value) ?? '');
    }
}
