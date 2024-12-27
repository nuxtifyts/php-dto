<?php

namespace Nuxtifyts\PhpDto\Exceptions;

use Exception;

class UnsupportedTypeException extends Exception
{
    protected const int UNKNOWN_TYPE = 0;
    protected const int EMPTY_TYPE = 1;
    protected const int INVALID_REFLECTION = 2;
    protected const int INVALID_TYPE = 3;

    public static function unknownType(?string $type = null): self
    {
        return new self(
            'Unknown type' . ($type ? " '{$type}'" : ''),
        );
    }

    public static function emptyType(): self
    {
        return new self(
            'Got empty type',
        );
    }

    public static function invalidReflection(): self
    {
        return new self(
            'Invalid reflection for type',
        );
    }

    public static function invalidType(): self
    {
        return new self(
            'Invalid type',
        );
    }
}
