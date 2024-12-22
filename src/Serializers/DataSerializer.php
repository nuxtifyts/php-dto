<?php

namespace Nuxtifyts\PhpDto\Serializers;

use Exception;
use Nuxtifyts\PhpDto\Concerns\SerializesArrayOfItems;
use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\Contracts\BaseData as BaseDataContract;
use Nuxtifyts\PhpDto\Contracts\SerializesArrayOfItems as SerializesArrayOfItemsContract;
use Nuxtifyts\PhpDto\Enums\Property\Type;
use Nuxtifyts\PhpDto\Exceptions\DeserializeException;
use Nuxtifyts\PhpDto\Exceptions\SerializeException;

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

            default => throw new SerializeException('Could not serialize array of BaseDataContract items')
        };
    }

    /**
     * @throws DeserializeException
     */
    protected function deserializeItem(mixed $item, PropertyContext $property): ?BaseDataContract
    {
        if (is_array($item)) {
            $typeContexts = $property->getFilteredTypeContexts(...self::supportedTypes())
                ?: $property->getFilteredSubTypeContexts(...self::supportedTypes());

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
        }

        return is_null($item) && $property->isNullable
            ? null
            : throw new DeserializeException('Could not deserialize BaseDataContract');
    }
}
