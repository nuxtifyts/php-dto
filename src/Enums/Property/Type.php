<?php

namespace Nuxtifyts\PhpDto\Enums\Property;

enum Type: string
{
    case FLOAT = 'float';
    case INT = 'int';
    case BOOLEAN = 'bool';
    case STRING = 'string';
    case BACKED_ENUM = 'backed_enum';
    case DATETIME = 'datetime';
    case DATA = 'data';
    case ARRAY = 'array';

    /** @var list<Type> */
    public const array SCALAR_TYPES = [
        self::FLOAT,
        self::INT,
        self::BOOLEAN,
        self::STRING,
    ];
}
