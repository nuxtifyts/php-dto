<?php

namespace Nuxtifyts\PhpDto\DataCiphers;

readonly class CipherConfig
{
    /**
     * @param class-string<DataCipher> $dataCipherClass
     */
    public function __construct(
        public string $dataCipherClass,
        public string $secret,
        public bool $encoded
    ) {
    }
}
