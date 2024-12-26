<?php

namespace Nuxtifyts\PhpDto\DataCiphers;

use Nuxtifyts\PhpDto\Exceptions\DataCipherException;

interface DataCipher
{
    /**
     * @throws DataCipherException
     */
    public static function cipher(
        mixed $data,
        string $secret,
        bool $encode = false
    ): string;

    /**
     * @throws DataCipherException
     */
    public static function decipher(
        string $data,
        string $secret,
        bool $decode = false
    ): mixed;
}
