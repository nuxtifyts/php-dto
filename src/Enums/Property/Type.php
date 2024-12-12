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
    case MIXED = 'mixed';
}
