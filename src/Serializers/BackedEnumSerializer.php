<?php

namespace Nuxtifyts\PhpDto\Serializers;

use BackedEnum;
use Exception;
use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\Enums\Property\Type;
use Nuxtifyts\PhpDto\Exceptions\DeserializeException;
use Nuxtifyts\PhpDto\Exceptions\SerializeException;
use Nuxtifyts\PhpDto\Serializers\Concerns\SerializesArrayOfItems;
use Nuxtifyts\PhpDto\Serializers\Contracts\SerializesArrayOfItems as SerializesArrayOfItemsContract;

class BackedEnumSerializer extends Serializer implements SerializesArrayOfItemsContract
{
    use SerializesArrayOfItems;

    public static function supportedTypes(): array
    {
        return [
            Type::BACKED_ENUM
        ];
    }

    /**
     * @throws SerializeException
     */
    protected function serializeItem(mixed $item, PropertyContext $property, object $object): string|int|null
    {
        return match(true) {
            is_null($item) && $property->isNullable => null,
            $item instanceof BackedEnum => $item->value,
            default => throw SerializeException::unableToSerializeScalarTypeItem()
        };
    }

    /**
     * @throws DeserializeException
     */
    protected function deserializeItem(mixed $item, PropertyContext $property): ?BackedEnum
    {
        if (is_null($item)) {
            return $property->isNullable
                ? null
                : throw DeserializeException::propertyIsNotNullable();
        }

        if (
            !is_string($item)
            && !is_integer($item)
            && !$item instanceof BackedEnum
        ) {
            throw DeserializeException::invalidValue();
        }

        $typeContexts = $property->getFilteredTypeContexts(...self::supportedTypes())
            ?: $property->getFilteredSubTypeContexts(...self::supportedTypes());

        foreach ($typeContexts as $typeContext) {
            try {
                if (!$typeContext->reflection?->implementsInterface(BackedEnum::class)) {
                    continue;
                }

                if ($item instanceof BackedEnum) {
                    if ($item instanceof ($typeContext->reflection->getName())) {
                        return $item;
                    } else {
                        continue;
                    }
                }

                $enumValue = call_user_func(
                    // @phpstan-ignore-next-line
                    [$typeContext->reflection->getName(), 'tryFrom'],
                    $item
                );

                if ($enumValue instanceof BackedEnum) {
                    return $enumValue;
                }
                // @codeCoverageIgnoreStart
            } catch (Exception) {
                continue;
            }
            // @codeCoverageIgnoreEnd
        }

        throw DeserializeException::unableToDeserializeBackedEnumItem();
    }
}
