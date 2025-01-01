<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Attributes;

use Nuxtifyts\PhpDto\Contexts\ClassContext;
use Nuxtifyts\PhpDto\Tests\Dummies\NonData\Human;
use Nuxtifyts\PhpDto\Tests\Dummies\Normalizers\HumanToPersonNormalizer;
use Nuxtifyts\PhpDto\Attributes\Class\WithNormalizer;
use Nuxtifyts\PhpDto\Tests\Dummies\PersonData;
use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\UsesClass;
use Throwable;

#[CoversClass(WithNormalizer::class)]
#[CoversClass(ClassContext::class)]
#[UsesClass(PersonData::class)]
#[UsesClass(HumanToPersonNormalizer::class)]
#[UsesClass(Human::class)]
final class WithNormalizerTest extends UnitCase
{
    /**
     * @throws Throwable
     */
    #[Test]
    public function will_validate_passed_arguments(): void
    {
        self::expectException(InvalidArgumentException::class);

        // @phpstan-ignore-next-line intentionally passing invalid normalizer class
        new WithNormalizer('InvalidNormalizer');
    }

    /**
     * @throws Throwable
     */
    #[Test]
    public function will_carry_normalizer_class_strings(): void
    {
        $normalizer = new WithNormalizer(HumanToPersonNormalizer::class);

        self::assertEquals([HumanToPersonNormalizer::class], $normalizer->classStrings);
    }

    /**
     * @throws Throwable
     */
    #[Test]
    public function will_use_normalizers_from_attribute(): void
    {
        $person = PersonData::from(new Human('John', 'Doe'));

        self::assertEquals('John', $person->firstName);
        self::assertEquals('Doe', $person->lastName);
    }
}
