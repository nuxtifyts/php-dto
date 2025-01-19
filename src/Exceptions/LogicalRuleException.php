<?php

namespace Nuxtifyts\PhpDto\Exceptions;

use Exception;

class LogicalRuleException extends Exception
{
    protected const int UNABLE_TO_CREATE_RULE_CODE = 1;

    public static function unableToCreateRule(string $message = 'Unable to create rule'): self
    {
        return new self($message, self::UNABLE_TO_CREATE_RULE_CODE);
    }
}
