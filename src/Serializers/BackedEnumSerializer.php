<?php

namespace Nuxtifyts\PhpDto\Serializers;

use ArrayAccess;
use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\Enums\Property\Type;
use BackedEnum;
use Nuxtifyts\PhpDto\Exceptions\DeserializeException;
use Nuxtifyts\PhpDto\Exceptions\SerializeException;
use Exception;

class BackedEnumSerializer extends Serializer
{
    /**
     * @inheritDoc
     */
    public static function supportedTypes(): array
    {
        return [
            Type::BACKED_ENUM
        ];
    }

    /**
     * @inheritDoc
     */
    public function serialize(PropertyContext $property, object $object): array
    {
        $value = $property->getValue($object);

        return [
            $property->propertyName => match (true) {
                $value instanceof BackedEnum => $value->value,
                $value === null && $property->isNullable => null,
                default => throw new SerializeException('Value is not a BackedEnum')
            }
        ];
    }

    /**
     * @inheritDoc
     */
    public function deserialize(PropertyContext $property, ArrayAccess|array $data): ?BackedEnum
    {
        $value = $data[$property->propertyName] ?? null;

        if (!is_string($value) && !is_integer($value) && !is_null($value)) {
            throw new DeserializeException('Value is not a string or integer');
        }

        if ($value !== null) {
            foreach ($property->getFilteredTypeContexts(...self::supportedTypes()) as $typeContext) {
                try {
                    if (!$typeContext->reflection?->implementsInterface(BackedEnum::class)) {
                        continue;
                    }

                    $enumValue = call_user_func(
                    // @phpstan-ignore-next-line
                        [$typeContext->reflection->getName(), 'tryFrom'],
                        $value
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
        }

        return $property->isNullable
            ? null
            : throw new DeserializeException('Could not deserialize BackedEnum');
    }
}
