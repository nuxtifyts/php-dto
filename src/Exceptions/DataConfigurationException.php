<?php

namespace Nuxtifyts\PhpDto\Exceptions;

use Exception;

class DataConfigurationException extends Exception
{
    protected const int INVALID_BASE_SERIALIZERS = 10000;

    public static function invalidBaseSerializers(): self
    {
        return new self('Invalid base serializers', self::INVALID_BASE_SERIALIZERS);
    }
}
