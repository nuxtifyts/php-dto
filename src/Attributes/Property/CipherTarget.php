<?php

namespace Nuxtifyts\PhpDto\Attributes\Property;

use Attribute;
use Nuxtifyts\PhpDto\DataCiphers\DataCipher;

#[Attribute(Attribute::TARGET_PROPERTY)]
class CipherTarget
{
    /**
     * @param class-string<DataCipher> $dataCipherClass
     */
    public function __construct(
        protected(set) string $dataCipherClass = DataCipher::class,
        protected(set) string $secret = '',
        protected(set) bool $encoded = false
    ) {
    }
}
