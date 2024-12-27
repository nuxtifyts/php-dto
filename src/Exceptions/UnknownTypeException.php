<?php

namespace Nuxtifyts\PhpDto\Exceptions;

use Exception;
use Nuxtifyts\PhpDto\Enums\Property\Type;

class UnknownTypeException extends Exception
{
    protected const int UNKNOWN_TYPE_EXCEPTION_CODE = 0;

    public static function unknownType(Type $type, Type ...$types): self
    {
        $types = array_map(
            static fn (Type $type): string => $type->value,
            $types
        );

        return new self(
            sprintf(
                'Unknown type "%s". Known types are: %s.',
                $type->value,
                implode(', ', $types)
            ),
            self::UNKNOWN_TYPE_EXCEPTION_CODE
        );
    }
}
