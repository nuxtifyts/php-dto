<?php

namespace Nuxtifyts\PhpDto\Tests\Dummies\ArrayAccessObjects;

use ArrayAccess;
use InvalidArgumentException;

/**
 * @implements ArrayAccess<string, mixed>
 */
class ArrayAccessPerson implements ArrayAccess
{
    public string $fullName;

    public function __construct(
        public string $firstName,
        public string $lastName
    ) {
        $this->fullName = $this->firstName . ' ' . $this->lastName;
    }

    public function offsetExists(mixed $offset): bool
    {
        return match ($offset) {
            'firstName', 'lastName', 'fullName' => true,
            default => false,
        };
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->{$offset};
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->{$offset} = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new InvalidArgumentException('Cannot unset offset');
    }
}
