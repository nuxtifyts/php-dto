<?php

namespace Nuxtifyts\PhpDto\Exceptions;

use Exception;

class DataCipherException extends Exception
{
    protected const int FAILED_TO_GET_NONCE_SIZE = 1;
    protected const int FAILED_TO_CIPHER_DATA = 2;
    protected const int FAILED_TO_DECIPHER_DATA = 3;
    protected const int FAILED_TO_STRINGIFY_VALUE = 4;
    protected const int INVALID_NONCE_SIZE = 5;

    public static function failedToGetNonceSize(): self
    {
        return new self(
            message: 'Failed to get nonce size.',
            code: self::FAILED_TO_GET_NONCE_SIZE
        );
    }

    public static function failedToCipherData(): self
    {
        return new self(
            message: 'Failed to cipher data.',
            code: self::FAILED_TO_CIPHER_DATA
        );
    }

    public static function failedToDecipherData(): self
    {
        return new self(
            message: 'Failed to decipher data.',
            code: self::FAILED_TO_DECIPHER_DATA
        );
    }

    public static function failedToStringifyValue(): self
    {
        return new self(
            message: 'Failed to stringify value.',
            code: self::FAILED_TO_STRINGIFY_VALUE
        );
    }

    public static function invalidNonceSize(): self
    {
        return new self(
            message: 'Invalid nonce size.',
            code: self::INVALID_NONCE_SIZE
        );
    }
}
