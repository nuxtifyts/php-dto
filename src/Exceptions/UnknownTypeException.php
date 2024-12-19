<?php

namespace Nuxtifyts\PhpDto\Exceptions;

use Exception;
use Nuxtifyts\PhpDto\Enums\Property\Type;

class UnknownTypeException extends Exception
{
    final protected function __construct(string $message = "")
    {
        parent::__construct($message);
    }

    public static function from(
        Type $type,
        Type ...$additionalTypes
    ): static {
        $types = implode(', ', array_column([$type, ...$additionalTypes], 'value'));

        return new static(
            "Unknown type '{$types}'"
        );
    }
}
