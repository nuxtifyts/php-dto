<?php

namespace Nuxtifyts\PhpDto\Exceptions;

use Exception;
use Throwable;

class DataCreationException extends Exception
{
    protected const int UNABLE_TO_CREATE_INSTANCE = 0;
    protected const int INVALID_PROPERTY = 1;
    protected const int UNABLE_TO_CREATE_EMPTY_INSTANCE = 2;
    protected const int UNABLE_TO_CLONE_INSTANCE_WITH_NEW_DATA = 3;
    protected const int INVALID_PARAMS_PASSED = 4;
    protected const int UNABLE_TO_CREATE_LAZY_INSTANCE = 5;

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

    public static function unableToCreateEmptyInstance(
        string $class,
        ?Throwable $previous = null
    ): self {
        return new self(
            message: "Unable to create empty instance of class {$class}",
            code: self::UNABLE_TO_CREATE_EMPTY_INSTANCE,
            previous: $previous
        );
    }

    public static function unableToCloneInstanceWithNewData(
        string $class,
        ?Throwable $previous = null
    ): self {
        return new self(
            message: "Unable to clone instance of class {$class} with new data",
            code: self::UNABLE_TO_CLONE_INSTANCE_WITH_NEW_DATA,
            previous: $previous
        );
    }

    public static function invalidParamsPassed(
        string $class,
        ?Throwable $previous = null
    ): self {
        return new self(
            message: "Invalid params passed to create method of class {$class}",
            code: self::INVALID_PARAMS_PASSED,
            previous: $previous
        );
    }

    public static function unableToCreateLazyInstance(
        string $class,
        ?Throwable $previous = null
    ): self {
        return new self(
            message: "Unable to create lazy instance of class {$class}",
            code: self::UNABLE_TO_CREATE_LAZY_INSTANCE,
            previous: $previous
        );
    }
}
