<?php

namespace Nuxtifyts\PhpDto\Exceptions;

use Exception;

class ValidationRuleException extends Exception
{
    protected const int INVALID_PARAMETERS = 1;

    public static function invalidParameters(): self
    {
        return new self('Invalid parameters', self::INVALID_PARAMETERS);
    }
}
