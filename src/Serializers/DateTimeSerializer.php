<?php

namespace Nuxtifyts\PhpDto\Serializers;

use ArrayAccess;
use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\Contracts\SerializesArrayOfItems as SerializesArrayOfItemsContract;
use Nuxtifyts\PhpDto\Concerns\SerializesArrayOfItems;
use Nuxtifyts\PhpDto\Enums\Property\Type;
use Nuxtifyts\PhpDto\Exceptions\DeserializeException;
use Nuxtifyts\PhpDto\Exceptions\SerializeException;

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

            default => throw new SerializeException('Value is not an instance of DateTimeInterface')
        };
    }

    /**
     * @throws DeserializeException
     */
    protected function deserializeItem(mixed $item, PropertyContext $property): ?DateTimeInterface
    {
        if (is_string($item)) {
            $typeContexts = $property->getFilteredTypeContexts(...self::supportedTypes())
                ?: $property->getFilteredSubTypeContexts(...self::supportedTypes());

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
        }

        return is_null($item) && $property->isNullable
            ? null
            : throw new DeserializeException('Could not deserialize DateTimeInterface');
    }
}
