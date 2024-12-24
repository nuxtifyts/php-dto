<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Documentation;

use Nuxtifyts\PhpDto\Attributes\PropertyAttributes\Computed;
use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use Throwable;

#[UsesClass(Computed::class)]
#[UsesClass(Data::class)]
final class PropertyAttributesExampleTest extends UnitCase
{
    /**
     * @throws Throwable
     */
    #[Test]
    public function will_be_able_to_handle_data_classes_with_computed_properties(): void
    {
        $person = new readonly class ('John', 'Doe') extends Data {
            #[Computed]
            public string $fullName;

            public function __construct(
                public string $firstName,
                public string $lastName
            ) {
                $this->fullName = $this->firstName . ' ' . $this->lastName;
            }
        };

        self::assertEquals(
            $serializedData = [
                'firstName' => 'John',
                'lastName' => 'Doe'
            ],
            $person->toArray(),
        );

        $personFrom = $person::from($serializedData);

        self::assertEquals('John Doe', $personFrom->fullName);
    }
}
