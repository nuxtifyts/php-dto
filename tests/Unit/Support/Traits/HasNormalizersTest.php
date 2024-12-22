<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Support\Traits;

use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Normalizers\ArrayAccessNormalizer;
use Nuxtifyts\PhpDto\Normalizers\ArrayNormalizer;
use Nuxtifyts\PhpDto\Normalizers\JsonStringNormalizer;
use Nuxtifyts\PhpDto\Normalizers\StdClassNormalizer;
use Nuxtifyts\PhpDto\Tests\Dummies\Support\DoesNotHaveAdditionalNormalizersDummy;
use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;
use Nuxtifyts\PhpDto\Tests\Dummies\Support\HasNormalizersDummy;
use Nuxtifyts\PhpDto\Tests\Dummies\Normalizers\DummyNormalizer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use Nuxtifyts\PhpDto\Tests\Dummies\PersonData;

#[CoversClass(Data::class)]
#[UsesClass(HasNormalizersDummy::class)]
#[UsesClass(DoesNotHaveAdditionalNormalizersDummy::class)]
#[UsesClass(DummyNormalizer::class)]
#[UsesClass(JsonStringNormalizer::class)]
#[UsesClass(StdClassNormalizer::class)]
#[UsesClass(ArrayAccessNormalizer::class)]
#[UsesClass(ArrayNormalizer::class)]
#[UsesClass(PersonData::class)]
final class HasNormalizersTest extends UnitCase
{
    #[Test]
    public function test_all_normalizers(): void
    {
        $normalizers = HasNormalizersDummy::getAllNormalizer();

        self::assertContains(DummyNormalizer::class, $normalizers);
        self::assertEquals([
            DummyNormalizer::class,
            JsonStringNormalizer::class,
            StdClassNormalizer::class,
            ArrayAccessNormalizer::class,
            ArrayNormalizer::class,
        ], $normalizers);
    }

    #[Test]
    public function test_customer_normalizers(): void
    {
        $normalizers = DoesNotHaveAdditionalNormalizersDummy::testNormalizers();

        self::assertEquals([], $normalizers);
    }

    #[Test]
    public function will_return_false_when_value_is_not_resolved_by_any_normalizer(): void
    {
        $normalized = HasNormalizersDummy::testNormalizeValue('test', PersonData::class);

        self::assertFalse($normalized);
    }

    #[Test]
    public function will_return_normalized_value_by_one_of_the_normalizers(): void
    {
        $normalized = HasNormalizersDummy::testNormalizeValue(['key' => 'value'], PersonData::class);

        self::assertEquals(['key' => 'value'], $normalized);
    }
}
