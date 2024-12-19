<?php

namespace Nuxtifyts\PhpDto\Exceptions;

use Exception;

class UnsupportedTypeException extends Exception
{
    final protected function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function from(string $type): static
    {
        return new static(
            "Unknown type '{$type}'"
        );
    }
}
