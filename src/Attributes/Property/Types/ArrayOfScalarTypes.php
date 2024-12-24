<?php

namespace Nuxtifyts\PhpDto\Attributes\Property\Types;

use Attribute;
use InvalidArgumentException;
use Nuxtifyts\PhpDto\Enums\Property\Type;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class ArrayOfScalarTypes
{
    /** @var list<Type> $types */
    private(set) array $types;

    /**
     * @param Type|list<Type> $types
     */
    public function __construct(
        Type|array $types = Type::SCALAR_TYPES
    ) {
        $typesArr = is_array($types) ? $types : [$types];

        if (
            $invalidTypes = array_diff(
                array_column($typesArr, 'value'),
                array_column(Type::SCALAR_TYPES, 'value'),
            )
        ) {
            throw new InvalidArgumentException(
                'Invalid type passed to ScalarTypeArray: ' . implode(', ', $invalidTypes)
            );
        }

        $this->types = $typesArr;
    }
}
