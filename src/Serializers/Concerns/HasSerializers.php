<?php

namespace Nuxtifyts\PhpDto\Serializers\Concerns;

use Nuxtifyts\PhpDto\Configuration\DataConfiguration;
use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\Contexts\TypeContext;
use Nuxtifyts\PhpDto\Enums\Property\Type;
use Nuxtifyts\PhpDto\Exceptions\UnknownTypeException;
use Nuxtifyts\PhpDto\Serializers\Serializer;
use Nuxtifyts\PhpDto\Exceptions\DataConfigurationException;

trait HasSerializers
{
    /**
     * @var list<Serializer>|null
     */
    private ?array $_serializers = null;

    /**
     * @return list<Serializer>
     *
     * @throws UnknownTypeException
     * @throws DataConfigurationException
     */
    protected function getSerializersFromPropertyContext(
        PropertyContext $propertyContext
    ): array {
        return array_values(array_filter(array_map(
            /** @param class-string<Serializer> $serializer */
            static fn (string $serializer): ?Serializer =>
                !empty(array_intersect(
                    array_column($propertyContext->types, 'value'),
                    array_column($serializer::supportedTypes(), 'value')
                )) ? new $serializer() : null,
            DataConfiguration::getInstance()->serializers->baseSerializers
        ))) ?: throw UnknownTypeException::unknownType(...$propertyContext->types);
    }

    /**
     * @param TypeContext<Type> $typeContext
     *
     * @return list<Serializer>
     *
     * @throws UnknownTypeException
     * @throws DataConfigurationException
     */
    protected function getSerializersFromTypeContext(
        TypeContext $typeContext,
    ): array {
        return array_values(array_filter(array_map(
            /** @param class-string<Serializer> $serializer */
            static fn (string $serializer): ?Serializer =>
                !empty(array_intersect(
                    array_column($typeContext->arrayElementTypes, 'value'),
                    array_column($serializer::supportedTypes(), 'value')
                )) ? new $serializer() : null,
            DataConfiguration::getInstance()->serializers->baseSerializers
        ))) ?: throw UnknownTypeException::unknownType(...$typeContext->arrayElementTypes);
    }

    /**
     * @return list<Serializer>
     *
     * @throws UnknownTypeException
     * @throws DataConfigurationException
     */
    public function serializers(): array
    {
        return $this->_serializers ??= $this->resolveSerializers();
    }

    /**
     * @return list<Serializer>
     */
    abstract protected function resolveSerializers(): array;
}
