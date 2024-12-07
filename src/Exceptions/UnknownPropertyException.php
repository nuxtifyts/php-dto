<?php

namespace Nuxtifyts\PhpDto\Exceptions;

use Exception;

class UnknownPropertyException extends Exception
{
    private function __construct(string $message, int $code = 0)
    {
        parent::__construct($message, $code);
    }

    public static function from(
        string $propertyName,
        object $object,
        int $code = 0
    ): self {
        return new self(
            "Unknown property '{$propertyName}' in object of type " . get_class($object),
            $code
        );
    }
}
