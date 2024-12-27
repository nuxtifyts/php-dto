<?php

namespace Nuxtifyts\PhpDto\Exceptions;

use Exception;
use Throwable;

class DataCreationException extends Exception
{
    protected const int UNABLE_TO_CREATE_INSTANCE = 0;
    protected const int INVALID_PROPERTY = 1;

    public static function unableToCreateInstance(
        string $class,
        ?Throwable $previous = null
    ): self {
        return new self(
            message: "Unable to create instance of class {$class}",
            code: self::UNABLE_TO_CREATE_INSTANCE,
            previous: $previous
        );
    }

    public static function invalidProperty(): self
    {
        return new self(
            message: 'Invalid property passed to create method',
            code: self::INVALID_PROPERTY
        );
    }
}
