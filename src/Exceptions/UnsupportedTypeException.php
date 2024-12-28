<?php

namespace Nuxtifyts\PhpDto\Exceptions;

use Exception;

class UnsupportedTypeException extends Exception
{
    protected const int EMPTY_TYPE = 1;
    protected const int INVALID_REFLECTION = 2;

    public static function emptyType(): self
    {
        return new self(
            'Got empty type',
            self::EMPTY_TYPE
        );
    }

    public static function invalidReflection(): self
    {
        return new self(
            'Invalid reflection for type',
            self::INVALID_REFLECTION
        );
    }
}
