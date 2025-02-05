<?php

namespace Nuxtifyts\PhpDto\Contexts\Concerns;

use Nuxtifyts\PhpDto\Contexts\TypeContext;
use Nuxtifyts\PhpDto\Enums\Property\Type;
use Nuxtifyts\PhpDto\Exceptions\UnsupportedTypeException;
use ReflectionProperty;

trait HasTypes
{
    /** @var list<TypeContext<Type>> */
    protected(set) public array $typeContexts = [];

    protected(set) public bool $isNullable = false;

    /** @var list<Type> */
    public array $types {
        get {
            return array_map(
                static fn (TypeContext $typeContext): Type => $typeContext->type,
                $this->typeContexts
            );
        }
    }

    /**
     * @throws UnsupportedTypeException
     */
    protected function syncTypesFromReflectionProperty(ReflectionProperty $property): void
    {
        $this->isNullable = $property->getType()?->allowsNull() ?? false;
        $this->typeContexts = TypeContext::getInstances($this);
    }
}
