<?php

namespace Nuxtifyts\PhpDto\Support\Traits;

use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\Contexts\TypeContext;
use Nuxtifyts\PhpDto\Enums\Property\Type;
use Nuxtifyts\PhpDto\Exceptions\UnknownTypeException;
use Nuxtifyts\PhpDto\Serializers\ArraySerializer;
use Nuxtifyts\PhpDto\Serializers\BackedEnumSerializer;
use Nuxtifyts\PhpDto\Serializers\DataSerializer;
use Nuxtifyts\PhpDto\Serializers\DateTimeSerializer;
use Nuxtifyts\PhpDto\Serializers\ScalarTypeSerializer;
use Nuxtifyts\PhpDto\Serializers\Serializer;

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
            self::serializersList()
        ))) ?: throw UnknownTypeException::from(...$propertyContext->types);
    }

    /**
     * @param TypeContext<Type> $typeContext
     *
     * @return list<Serializer>
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
            self::serializersList()
        )));
    }

    /**
     * @return list<class-string<Serializer>>
     */
    protected static function serializersList(): array
    {
        return [
            ArraySerializer::class,
            DataSerializer::class,
            DateTimeSerializer::class,
            BackedEnumSerializer::class,
            ScalarTypeSerializer::class,
        ];
    }

    /**
     * @return list<Serializer>
     *
     * @throws UnknownTypeException
     */
    public function serializers(): array
    {
        return $this->_serializers ??= $this->resolveSerializers();
    }

    abstract protected function resolveSerializers(): array;
}
