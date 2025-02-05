<?php

namespace Nuxtifyts\PhpDto\Exceptions;

use Exception;

class FallbackResolverException extends Exception
{
    protected const int UNABLE_TO_FIND_RESOLVER_CLASS = 0;

    public static function unableToFindResolverClass(string $resolverClass): self
    {
        return new self(
            "Unable to find resolver class: {$resolverClass}",
            self::UNABLE_TO_FIND_RESOLVER_CLASS
        );
    }
}
