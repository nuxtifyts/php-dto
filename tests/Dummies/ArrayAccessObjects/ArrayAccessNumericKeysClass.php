<?php

namespace Nuxtifyts\PhpDto\Tests\Dummies\ArrayAccessObjects;

use ArrayAccess;
use InvalidArgumentException;

/**
 * @implements ArrayAccess<int, mixed>
 */
final class ArrayAccessNumericKeysClass implements ArrayAccess
{
    public function __construct(
        public string $firstProperty,
        public string $sendProperty
    ) {
    }

    public function offsetExists(mixed $offset): bool
    {
        return match($offset) {
            1, 2 => true,
            default => false
        };
    }

    public function offsetGet(mixed $offset): mixed
    {
        $propertyName = match($offset) {
            1 => 'firstProperty',
            2 => 'sendProperty',
            default => throw new InvalidArgumentException('Offset does not exist')
        };

        return $this->{$propertyName};
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (!is_string($value)) {
            throw new InvalidArgumentException('Value must be a string');
        }

        $propertyName = match($offset) {
            1 => 'firstProperty',
            2 => 'sendProperty',
            default => throw new InvalidArgumentException('Offset does not exist')
        };

        $this->{$propertyName} = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new InvalidArgumentException('Cannot unset offset');
    }
}
