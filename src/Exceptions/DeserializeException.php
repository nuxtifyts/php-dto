<?php

namespace Nuxtifyts\PhpDto\Exceptions;

use Exception;
use Throwable;

class DeserializeException extends Exception
{
    public const int GENERIC_ERROR_CODE = 0;
    public const int INVALID_VALUE_ERROR_CODE = 1;
    public const int NO_SERIALIZERS_ERROR_CODE = 2;

    public function __construct(
        string $message = 'Failed to deserialize data',
        int $code = self::GENERIC_ERROR_CODE,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
