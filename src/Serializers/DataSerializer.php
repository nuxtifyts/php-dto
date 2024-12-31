<?php

namespace Nuxtifyts\PhpDto\Serializers;

use Exception;
use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\Contexts\TypeContext;
use Nuxtifyts\PhpDto\Contracts\BaseData as BaseDataContract;
use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Enums\Property\Type;
use Nuxtifyts\PhpDto\Exceptions\DeserializeException;
use Nuxtifyts\PhpDto\Exceptions\SerializeException;
use Nuxtifyts\PhpDto\Serializers\Contracts\SerializesArrayOfItems as SerializesArrayOfItemsContract;
use Nuxtifyts\PhpDto\Serializers\Concerns\SerializesArrayOfItems;

class DataSerializer extends Serializer implements SerializesArrayOfItemsContract
{
    use SerializesArrayOfItems;

    public static function supportedTypes(): array
    {
        return [
            Type::DATA
        ];
    }

    /**
     * @return ?array<string, mixed>
     *
     * @throws SerializeException
     */
    protected function serializeItem(mixed $item, PropertyContext $property, object $object): ?array
    {
        return match (true) {
            $item === null && $property->isNullable => null,

            $item instanceof BaseDataContract => $item->jsonSerialize(),

            default => throw SerializeException::unableToSerializeDataItem()
        };
    }

    /**
     * @throws DeserializeException
     */
    protected function deserializeItem(mixed $item, PropertyContext $property): ?BaseDataContract
    {
        $typeContexts = $property->getFilteredTypeContexts(...self::supportedTypes())
            ?: $property->getFilteredSubTypeContexts(...self::supportedTypes());

        if (is_array($item)) {
            foreach ($typeContexts as $typeContext) {
                try {
                    if (!$typeContext->reflection?->implementsInterface(BaseDataContract::class)) {
                        continue;
                    }

                    $deserializedValue = call_user_func(
                        // @phpstan-ignore-next-line
                        [$typeContext->reflection->getName(), 'from'],
                        $item
                    );

                    if (!$deserializedValue instanceof BaseDataContract) {
                        throw new Exception('Not an instance of BaseDataContract');
                    }

                    return $deserializedValue;
                    // @codeCoverageIgnoreStart
                } catch (Exception) {
                    continue;
                }
                // @codeCoverageIgnoreEnd
            }
        } elseif ($item instanceof Data) {
            if (
                array_any(
                    $typeContexts,
                    static fn (TypeContext $type) => (bool) $type->reflection?->isInstance($item)
                )
            ) {
                return $item;
            }
        }

        return is_null($item) && $property->isNullable
            ? null
            : throw DeserializeException::unableToDeserializeDataItem();
    }
}
