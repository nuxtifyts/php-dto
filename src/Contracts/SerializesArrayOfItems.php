<?php

namespace Nuxtifyts\PhpDto\Contracts;

use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\Exceptions\DeserializeException;
use Nuxtifyts\PhpDto\Exceptions\SerializeException;

interface SerializesArrayOfItems
{
    /**
     * @return array<string, ?array<array-key, mixed>>
     *
     * @throws SerializeException
     */
    public function serializeArrayOfItems(
        PropertyContext $property,
        object $object
    ): array;

    /**
     * @param array<string, mixed> $data
     *
     * @return ?array<array-key, mixed>
     *
     * @throws DeserializeException
     */
    public function deserializeArrayOfItems(
        PropertyContext $property,
        array $data
    ): ?array;
}
