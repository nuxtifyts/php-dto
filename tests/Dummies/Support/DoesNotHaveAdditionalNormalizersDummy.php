<?php

namespace Nuxtifyts\PhpDto\Tests\Dummies\Support;

use Nuxtifyts\PhpDto\Normalizers\Normalizer;
use Nuxtifyts\PhpDto\Support\Traits\HasNormalizers;

final class DoesNotHaveAdditionalNormalizersDummy
{
    use HasNormalizers;

    /**
     * @return list<class-string<Normalizer>>
     */
    public static function testNormalizers(): array
    {
        return self::normalizers();
    }
}
