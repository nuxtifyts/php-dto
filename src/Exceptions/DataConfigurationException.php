<?php

namespace Nuxtifyts\PhpDto\Exceptions;

use Exception;

class DataConfigurationException extends Exception
{
    protected const int INVALID_BASE_SERIALIZERS = 10000;

    protected const int INVALID_BASE_NORMALIZERS = 20000;

    public static function invalidBaseSerializers(): self
    {
        return new self('Invalid base serializers', self::INVALID_BASE_SERIALIZERS);
    }

    public static function invalidBaseNormalizers(): self
    {
        return new self('Invalid base normalizers', self::INVALID_BASE_NORMALIZERS);
    }
}
