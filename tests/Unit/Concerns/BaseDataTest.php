<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Concerns;

use Nuxtifyts\PhpDto\Concerns\BaseData;
use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;
use Nuxtifyts\PhpDto\Tests\Dummies\PersonData;
use Nuxtifyts\PhpDto\Tests\Dummies\UnionTypedData;
use Nuxtifyts\PhpDto\Helper\ReflectionPropertyHelper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use Throwable;

#[CoversClass(BaseData::class)]
#[CoversClass(ReflectionPropertyHelper::class)]
#[UsesClass(PersonData::class)]
#[UsesClass(UnionTypedData::class)]
final class BaseDataTest extends UnitCase
{
    /**
     * @throws Throwable
     */
    #[Test]
    public function base_data_supports_scalar_types(): void
    {
        $person = new PersonData('John', 'Doe');

        self::assertEquals(
            [
                'firstName' => 'John',
                'lastName' => 'Doe',
                'fullName' => 'John Doe'
            ],
            $personData = $person->jsonSerialize()
        );

        $person = PersonData::from($personData);

        self::assertEquals('John', $person->firstName);
        self::assertEquals('Doe', $person->lastName);
        self::assertEquals('John Doe', $person->fullName);
    }

    /**
     * @throws Throwable
     */
    #[Test]
    #[DataProvider('base_data_support_union_types_provider')]
    public function base_data_support_union_types(
        int|string|null $value,
        int|string|null $expected
    ): void {
        $union = new UnionTypedData($value);

        self::assertEquals(
            [
                'value' => $expected
            ],
            $unionData = $union->jsonSerialize()
        );

        $union = UnionTypedData::from($unionData);

        self::assertEquals($expected, $union->value);
    }

    /**
     * @phpstan-ignore-next-line
     */
    public static function base_data_support_union_types_provider(): array
    {
        return [
            'Integer type' => [42, 42],
            'String type' => ['string value', 'string value'],
            'Null type' => [null, null]
        ];
    }
}
