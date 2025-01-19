<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Support;

use Nuxtifyts\PhpDto\Support\Collection;
use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(Collection::class)]
final class CollectionTest extends UnitCase
{
    /**
     * @param Collection<array-key, mixed> $collection
     * @param array<string, mixed> $functionParams
     */
    #[Test]
    #[DataProvider('push_function_provider')]
    #[DataProvider('put_function_provider')]
    #[DataProvider('first_function_provider')]
    #[DataProvider('map_function_provider')]
    #[DataProvider('collapse_function_provider')]
    #[DataProvider('flatten_function_provider')]
    #[DataProvider('all_function_provider')]
    #[DataProvider('validation_functions_provider')]
    public function will_be_able_to_perform_functions(
        Collection $collection,
        string $functionName,
        array $functionParams,
        mixed $expected
    ): void {
        $result = $collection->{$functionName}(...$functionParams);

        if ($expected instanceof Collection) {
            self::assertInstanceOf(Collection::class, $result);
            self::assertCollection($result, $expected);
        } else {
            self::assertEquals($expected, $result);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public static function push_function_provider(): array
    {
        return [
            'push' => [
                'collection' => new Collection([1, 2, 3]),
                'functionName' => 'push',
                'functionParams' => [ 'item' => 4 ],
                'expected' => new Collection([1, 2, 3, 4])
            ]
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function put_function_provider(): array
    {
        return [
            'put in new key' => [
                'collection' => new Collection([1, 2, 3]),
                'functionName' => 'put',
                'functionParams' => [ 'key' => 3, 'value' => 4 ],
                'expected' => new Collection([1, 2, 3, 4])
            ],
            'put in existing key will override value' => [
                'collection' => new Collection([1, 2, 3]),
                'functionName' => 'put',
                'functionParams' => [ 'key' => 0, 'value' => 4 ],
                'expected' => new Collection([4, 2, 3])
            ]
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function first_function_provider(): array
    {
        return [
            'first without callable and non empty collection' => [
                'collection' => new Collection([1, 2, 3]),
                'functionName' => 'first',
                'functionParams' => [],
                'expected' => 1
            ],
            'first without callable and empty collection' => [
                'collection' => new Collection([]),
                'functionName' => 'first',
                'functionParams' => [],
                'expected' => null
            ],
            'first with callable and existing item that will meet requirements' => [
                'collection' => new Collection([1, 2, 3]),
                'functionName' => 'first',
                'functionParams' => [ 'callable' => static fn (int $item) => $item === 2 ],
                'expected' => 2
            ],
            'first with callable and no item that will meet requirements' => [
                'collection' => new Collection([1, 2, 3]),
                'functionName' => 'first',
                'functionParams' => [ 'callable' => static fn (int $item) => $item === 4 ],
                'expected' => null
            ]
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function map_function_provider(): array
    {
        return [
            'map' => [
                'collection' => new Collection([1, 2, 3]),
                'functionName' => 'map',
                'functionParams' => [ 'callable' => static fn (int $item) => $item * 2 ],
                'expected' => new Collection([2, 4, 6])
            ]
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function collapse_function_provider(): array
    {
        return [
            'collapse' => [
                'collection' => new Collection([
                    new Collection([ 'a' => 1, 2, 3]),
                    new Collection([4, 5, 6]),
                    new Collection([7, 8, 9])
                ]),
                'functionName' => 'collapse',
                'functionParams' => [],
                'expected' => new Collection([1, 2, 3, 4, 5, 6, 7, 8, 9])
            ],

        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function flatten_function_provider(): array
    {
        return [
            'flatten' => [
                'collection' => new Collection([
                    'a1' => new Collection([
                        'a1.1' => 1.1,
                        'a1.2' => new Collection([
                            'a1.2.1' => 1.21,
                            'a1.2.2' => 1.22,
                            'a1.2.3' => new Collection([
                                'a1.2.3.1' => 1.231,
                                'a1.2.3.2' => 1.232,
                                'a1.2.3.3' => 1.233
                            ])
                        ])
                    ])
                ]),
                'functionName' => 'flatten',
                'functionParams' => [],
                'expected' => new Collection([
                    'a1.1' => 1.1,
                    'a1.2.1' => 1.21,
                    'a1.2.2' => 1.22,
                    'a1.2.3.1' => 1.231,
                    'a1.2.3.2' => 1.232,
                    'a1.2.3.3' => 1.233
                ])
            ]
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function all_function_provider(): array
    {
        return [
            'all' => [
                'collection' => new Collection([1, 2, 3]),
                'functionName' => 'all',
                'functionParams' => [],
                'expected' => [1, 2, 3]
            ]
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function validation_functions_provider(): array
    {
        return [
            'isEmpty' => [
                'collection' => new Collection([]),
                'functionName' => 'isEmpty',
                'functionParams' => [],
                'expected' => true
            ],
            'isNotEmpty' => [
                'collection' => new Collection([1, 2, 3]),
                'functionName' => 'isNotEmpty',
                'functionParams' => [],
                'expected' => true
            ],
            'every' => [
                'collection' => new Collection([1, 2, 3]),
                'functionName' => 'every',
                'functionParams' => [ 'callable' => static fn (int $item) => $item > 0 ],
                'expected' => true
            ],
            'some' => [
                'collection' => new Collection([1, 2, 3]),
                'functionName' => 'some',
                'functionParams' => [ 'callable' => static fn (int $item) => $item === 2 ],
                'expected' => true
            ],
        ];
    }

    /**
     * @param Collection<array-key, mixed> $collection
     * @param Collection<array-key, mixed> $expected
     */
    private static function assertCollection(Collection $collection, Collection $expected): void
    {
        self::assertEquals($expected->all(), $collection->all());
    }
}
