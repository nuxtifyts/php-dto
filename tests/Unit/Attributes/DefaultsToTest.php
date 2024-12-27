<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Attributes;

use Nuxtifyts\PhpDto\Attributes\Property\DefaultsTo;
use Nuxtifyts\PhpDto\Attributes\Property\Types\ArrayOfScalarTypes;
use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Enums\Property\Type;
use Nuxtifyts\PhpDto\Exceptions\FallbackResolverException;
use Nuxtifyts\PhpDto\FallbackResolver\FallbackConfig;
use Nuxtifyts\PhpDto\Pipelines\DeserializePipeline\ResolveDefaultDataPipe;
use Nuxtifyts\PhpDto\Tests\Dummies\FallbackResolvers\DummyUserFallbackResolver;
use Nuxtifyts\PhpDto\Tests\Dummies\UserData;
use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use Throwable;

#[CoversClass(DefaultsTo::class)]
#[CoversClass(FallbackResolverException::class)]
#[CoversClass(FallbackConfig::class)]
#[CoversClass(PropertyContext::class)]
#[CoversClass(ResolveDefaultDataPipe::class)]
#[UsesClass(DummyUserFallbackResolver::class)]
#[UsesClass(UserData::class)]
final class DefaultsToTest extends UnitCase
{
    /**
     * @throws Throwable
     */
    #[Test]
    public function should_throw_an_exception_if_the_resolver_class_does_not_implement_fallback_resolver_interface(): void
    {
        self::expectException(FallbackResolverException::class);

        new DefaultsTo(UserData::class);
    }

    /**
     * @param array<string, mixed> $arrayData
     * @param array<string, mixed> $expectedSerializedData
     *
     * @throws Throwable
     */
    #[Test]
    #[DataProvider('should_be_able_to_resolve_default_values_data_provider')]
    public function should_be_able_to_resolve_default_values(
        Data $object,
        array $arrayData,
        array $expectedSerializedData
    ): void {
        self::assertEquals(
            $expectedSerializedData,
            $object::from($arrayData)->toArray()
        );
    }

    /**
     * @return array<string, mixed>
     */
    public static function should_be_able_to_resolve_default_values_data_provider(): array
    {
        return [
            'Resolves default scalar type value' => [
                'object' => new readonly class ('') extends Data {
                    public function __construct(
                        #[DefaultsTo('John')]
                        public string $name
                    ) {
                    }
                },
                'arrayData' => [],
                'expectedSerializedData' => [
                    'name' => 'John'
                ]
            ],
            'Resolves default complex type value' => [
                'object' => new readonly class (
                    new UserData('John', 'Doe')
                ) extends Data {
                    public function __construct(
                        #[DefaultsTo(DummyUserFallbackResolver::class)]
                        public UserData $userData
                    ) {
                    }
                },
                'arrayData' => [],
                'expectedSerializedData' => [
                    'userData' => [
                        'firstName' => 'John',
                        'lastName' => 'Doe'
                    ]
                ]
            ],
            'Resolves array of scalar type values' => [
                'object' => new readonly class([]) extends Data {
                    /**
                     * @param list<string> $names
                     */
                    public function __construct(
                        #[ArrayOfScalarTypes(Type::STRING)]
                        #[DefaultsTo(['John', 'Jane'])]
                        public array $names
                    ) {
                    }
                },
                'arrayData' => [],
                'expectedSerializedData' => [
                    'names' => ['John', 'Jane']
                ]
            ],
            'Allows pure php way of defaulting 1' => [
                'object' => new readonly class ('') extends Data {
                    public function __construct(
                        public string $name = 'John'
                    ) {
                    }
                },
                'arrayData' => [],
                'expectedSerializedData' => [
                    'name' => 'John'
                ]
            ],
            'Allows pure php way of defaulting 2' => [
                'object' => new readonly class([]) extends Data {
                    /**
                     * @param list<string> $names
                     */
                    public function __construct(
                        #[ArrayOfScalarTypes(Type::STRING)]
                        public array $names = ['John', 'Jane']
                    ) {
                    }
                },
                'arrayData' => [],
                'expectedSerializedData' => [
                    'names' => ['John', 'Jane']
                ]
            ],
        ];
    }
}
