<?php

namespace Nuxtifyts\PhpDto\Exceptions;

use Exception;
use Throwable;

class DataValidationException extends Exception
{
    protected const int INVALID_DATA_CODE = 0;

    public static function invalidData(string $message, int $code = self::INVALID_DATA_CODE, ?Throwable $previous = null): self
    {
        return new self($message, $code, $previous);
    }
}
