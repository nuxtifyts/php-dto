<?php

namespace Nuxtifyts\PhpDto\Tests\Dummies\Support;

use Nuxtifyts\PhpDto\Normalizers\Concerns\HasNormalizers;
use Nuxtifyts\PhpDto\Normalizers\Normalizer;

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
