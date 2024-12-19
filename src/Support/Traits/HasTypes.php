<?php

namespace Nuxtifyts\PhpDto\Support\Traits;

use Nuxtifyts\PhpDto\Contexts\TypeContext;
use Nuxtifyts\PhpDto\Enums\Property\Type;
use Nuxtifyts\PhpDto\Exceptions\UnsupportedTypeException;
use ReflectionProperty;

trait HasTypes
{
    /** @var list<TypeContext<Type>> */
    protected(set) array $typeContexts = [];

    protected(set) bool $isNullable = false;

    /** @var list<Type> */
    public array $types {
        get => array_map(
            static fn (TypeContext $typeContext): Type => $typeContext->type,
            $this->typeContexts
        );
    }

    /**
     * @throws UnsupportedTypeException
     */
    protected function syncTypesFromReflectionProperty(ReflectionProperty $property): void
    {
        $this->isNullable = $property->getType()?->allowsNull() ?? false;
        $this->typeContexts = TypeContext::getInstances($property);
    }
}
