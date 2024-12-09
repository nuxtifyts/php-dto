<?php

namespace Nuxtifyts\PhpDto\Exceptions;

use Exception;
use Throwable;

class SerializeException extends Exception
{
    public const int GENERIC_ERROR_CODE = 0;
    public const int NO_SERIALIZERS_ERROR_CODE = 1;

    public function __construct(
        string $message = "",
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
