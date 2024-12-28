<?php

namespace Nuxtifyts\PhpDto\Tests\Dummies\DataCiphers;

use Nuxtifyts\PhpDto\DataCiphers\DataCipher;
use Nuxtifyts\PhpDto\Exceptions\DataCipherException;

class UselessDataCipher implements DataCipher
{

    /**
     * @throws DataCipherException
     */
    public static function cipher(mixed $data, string $secret, bool $encode = false): never
    {
        throw DataCipherException::failedToCipherData();
    }

    /**
     * @throws DataCipherException
     */
    public static function decipher(string $data, string $secret, bool $decode = false): never
    {
        throw DataCipherException::failedToDecipherData();
    }
}
