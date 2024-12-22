<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Documentation;

use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Tests\Dummies\DocsDummies\UserDetailsData;
use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use Throwable;

#[UsesClass(UserDetailsData::class)]
final class ReadmeExampleTest extends UnitCase
{
    /**
     * @param class-string<Data> $dtoClass
     * @param array<string, mixed> $data
     * @param array<string, mixed> $expectedDtoProperties
     * @param array<string, mixed> $expectedSerializedProperties
     *
     * @throws Throwable
     */
    #[Test]
    #[DataProvider('data_provider')]
    public function will_perform_serialize_and_deserialize_on_data_transfer_objects_from_docs(
        string $dtoClass,
        array $data,
        array $expectedDtoProperties,
        array $expectedSerializedProperties,
    ): void {
        $dtoObject = $dtoClass::from($data);

        foreach ($expectedDtoProperties as $property => $value) {
            self::assertEquals($value, $dtoObject->{$property});
        }

        self::assertEquals(
            $expectedSerializedProperties,
            $dtoObject->jsonSerialize()
        );
    }

    /**
     * @return array<string, array{
     *     dtoClass: class-string<Data>,
     *     data: array<string, mixed>,
     *     expectedDtoProperties: array<string, mixed>,
     *     expectedSerializedProperties: array<string, mixed>
     * }>
     */
    public static function basic_examples_data_provider(): array
    {
        // @phpstan-ignore-next-line
        return [
            'First basic example' => [
                'dtoClass' => UserDetailsData::class,
                'data' => $data = [
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'fullName' => 'John Doe'
                ],
                'expectedDtoProperties' => [
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'fullName' => 'John Doe'
                ],
                'expectedSerializedProperties' => $data
            ]
        ];
    }
}
