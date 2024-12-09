<?php

namespace Nuxtifyts\PhpDto\Enums\Property;

enum Type: string
{
    case FLOAT = 'float';
    case INT = 'int';
    case BOOLEAN = 'bool';
    case STRING = 'string';
    case MIXED = 'mixed';

    /**
     * @param list<string> $types
     *
     * @return list<Type>
     */
    public static function fromReflectionProperty(array $types): array
    {
        return array_map(
            static fn(string $type): Type => match($type) {
                'double', 'float' => Type::FLOAT,
                'int', 'integer' => Type::INT,
                'bool', 'boolean' => Type::BOOLEAN,
                'string' => Type::STRING,
                default => Type::MIXED
            },
            $types
        );
    }
}
