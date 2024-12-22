<?php

namespace Nuxtifyts\PhpDto\Serializers;

use BackedEnum;
use Exception;
use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\Contracts\SerializesArrayOfItems as SerializesArrayOfItemsContract;
use Nuxtifyts\PhpDto\Concerns\SerializesArrayOfItems;
use Nuxtifyts\PhpDto\Enums\Property\Type;
use Nuxtifyts\PhpDto\Exceptions\DeserializeException;
use Nuxtifyts\PhpDto\Exceptions\SerializeException;

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
            default => throw new SerializeException('Could not serialize array of BackedEnum items')
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
                : throw new DeserializeException('Property is not nullable');
        }

        if (!is_string($item) && !is_integer($item)) {
            throw new DeserializeException('Value is not a string or integer');
        }

        $typeContexts = $property->getFilteredTypeContexts(...self::supportedTypes())
            ?: $property->getFilteredSubTypeContexts(...self::supportedTypes());

        foreach ($typeContexts as $typeContext) {
            try {
                if (!$typeContext->reflection?->implementsInterface(BackedEnum::class)) {
                    continue;
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

        throw new DeserializeException('Could not deserialize BackedEnum');
    }
}
