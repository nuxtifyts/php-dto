<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Concerns;

use Nuxtifyts\PhpDto\Attributes\Property\Computed;
use Nuxtifyts\PhpDto\Contracts\CloneableData as CloneableDataContract;
use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Exceptions\DataCreationException;
use Nuxtifyts\PhpDto\Tests\Dummies\AddressData;
use Nuxtifyts\PhpDto\Tests\Dummies\CoordinatesData;
use Nuxtifyts\PhpDto\Tests\Dummies\CountryData;
use Nuxtifyts\PhpDto\Tests\Dummies\PersonData;
use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use DateTimeImmutable;
use Throwable;

#[CoversClass(Data::class)]
#[CoversClass(DataCreationException::class)]
#[UsesClass(PersonData::class)]
#[UsesClass(Computed::class)]
#[UsesClass(AddressData::class)]
#[UsesClass(CoordinatesData::class)]
#[UsesClass(CountryData::class)]
final class CloneableDataTest extends UnitCase
{
    /**
     * @throws Throwable
     */
    #[Test]
    public function will_throw_an_exception_if_invalid_value_for_property_is_passed(): void
    {
        $person = new PersonData(firstName: 'John', lastName: 'Doe');

        self::expectException(DataCreationException::class);

        $person->with(firstName: new DateTimeImmutable());
    }

    /**
     * @throws Throwable
     */
    #[Test]
    public function will_throw_an_exception_if_dto_declaration_is_invalid(): void
    {
        $object = new readonly class ('firstName', 'lastName') extends Data {
            #[Computed]
            public string $fullName;
            public string $familyName;

            public function __construct(
                public string $firstName,
                string $lastName,
            ) {
                $this->familyName = $lastName;
                $this->fullName = $this->firstName . ' ' . $this->familyName;
            }
        };

        self::expectException(DataCreationException::class);
        $object->with(lastName: 'Doe');
    }

    /**
     * @param array<string, mixed> $args
     * @param array<string, mixed> $expectedProperties
     *
     * @throws Throwable
     */
    #[Test]
    #[DataProvider('will_be_able_to_clone_data_provider')]
    public function will_be_able_to_clone_data(
        CloneableDataContract $object,
        array $args,
        array $expectedProperties
    ): void {
        $newObject = $object->with(...$args);

        self::assertNotSame($object, $newObject);

        foreach ($expectedProperties as $propertyName => $value) {
            self::assertObjectHasProperty($propertyName, $newObject);
            self::assertEquals($value, $newObject->{$propertyName});
        }
    }

    /**
     * @return array<string, mixed>
     */
    public static function will_be_able_to_clone_data_provider(): array
    {
        return [
            'Will be able to clone scalar type properties' => [
                'object' => new PersonData('John', 'Doe'),
                'args' => [
                    'firstName' => 'Jane',
                    'lastName' => 'Doe',
                ],
                'expectedProperties' => [
                    'firstName' => 'Jane',
                    'lastName' => 'Doe',
                ],
            ],
            'Will be able to clone data type properties' => [
                'object' => new AddressData(
                    '1234 Elm St',
                    'City',
                    'State',
                    '12345',
                    new CountryData(
                        'XX',
                        'Country'
                    ),
                    null
                ),
                'args' => [
                    'country' => new CountryData(
                        'YY',
                        'Country 2'
                    ),
                    'coordinates' => new CoordinatesData(
                        1.234,
                        5.678
                    )
                ],
                'expectedProperties' => [
                    'street' => '1234 Elm St',
                    'city' => 'City',
                    'state' => 'State',
                    'zip' => '12345',
                    'country' => new CountryData(
                        'YY',
                        'Country 2'
                    ),
                    'coordinates' => new CoordinatesData(
                        1.234,
                        5.678
                    )
                ],
            ],
            'Will be able to update computed properties' => [
                'object' => new PersonData('John', 'Doe'),
                'args' => [
                    'firstName' => 'Jane',
                    'lastName' => 'Doe',
                ],
                'expectedProperties' => [
                    'firstName' => 'Jane',
                    'lastName' => 'Doe',
                    'fullName' => 'Jane Doe',
                ],
            ]
        ];
    }
}
