<?php

namespace Nuxtifyts\PhpDto\Serializers;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\Contexts\TypeContext;
use Nuxtifyts\PhpDto\Enums\Property\Type;
use Nuxtifyts\PhpDto\Exceptions\DeserializeException;
use Nuxtifyts\PhpDto\Exceptions\SerializeException;
use Nuxtifyts\PhpDto\Serializers\Contracts\SerializesArrayOfItems as SerializesArrayOfItemsContract;
use Nuxtifyts\PhpDto\Serializers\Concerns\SerializesArrayOfItems;

class DateTimeSerializer extends Serializer implements SerializesArrayOfItemsContract
{
    use SerializesArrayOfItems;

    public static function supportedTypes(): array
    {
        return [
            Type::DATETIME
        ];
    }

    /**
     * @throws SerializeException
     */
    protected function serializeItem(mixed $item, PropertyContext $property, object $object): ?string
    {
        return match (true) {
            $item === null && $property->isNullable => null,

            $item instanceof DateTimeInterface => $item->format(DateTimeInterface::ATOM),

            default => throw SerializeException::unableToSerializeDateTimeItem()
        };
    }

    /**
     * @throws DeserializeException
     */
    protected function deserializeItem(mixed $item, PropertyContext $property): ?DateTimeInterface
    {
        $typeContexts = $property->getFilteredTypeContexts(...self::supportedTypes())
            ?: $property->getFilteredSubTypeContexts(...self::supportedTypes());

        if (is_string($item)) {
            foreach ($typeContexts as $typeContext) {
                try {
                    if (!$typeContext->reflection?->implementsInterface(DateTimeInterface::class)) {
                        continue;
                    }

                    if ($typeContext->reflection->isInterface()) {
                        return new DateTimeImmutable($item);
                    }

                    $deserializedValue = $typeContext->reflection->newInstance($item);

                    if (!$deserializedValue instanceof DateTimeInterface) {
                        throw new Exception('Not an instance of DateTimeInterface');
                    }

                    return $deserializedValue;
                    // @codeCoverageIgnoreStart
                } catch (Exception) {
                    continue;
                }
                // @codeCoverageIgnoreEnd
            }
        } elseif ($item instanceof DateTimeInterface) {
            if (array_any(
                $typeContexts,
                static fn(TypeContext $typeContext) => (bool) $typeContext->reflection?->isInstance($item)
            )) {
                return $item;
            }
        }

        return is_null($item) && $property->isNullable
            ? null
            : throw DeserializeException::unableToDeserializeDateTimeItem();
    }
}
