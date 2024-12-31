<?php

namespace Nuxtifyts\PhpDto\Exceptions;

use Exception;
use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\DataRefiners\DataRefiner;

class InvalidRefiner extends Exception
{
    protected const int EMPTY_TYPE_CONTEXTS_CODE = 1;

    public static function emptyTypeContexts(): self
    {
        return new self(
            'Property does not have any type contexts',
            self::EMPTY_TYPE_CONTEXTS_CODE
        );
    }
}
