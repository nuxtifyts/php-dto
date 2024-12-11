<?php

namespace Nuxtifyts\PhpDto\Serializers;

use ArrayAccess;
use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\Enums\Property\Type;
use BackedEnum;
use Nuxtifyts\PhpDto\Exceptions\DeserializeException;
use Nuxtifyts\PhpDto\Exceptions\SerializeException;

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
            $property->propertyName => match(true) {
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
            foreach ($property->enumReflections as $enumReflection) {
                /** @var class-string<BackedEnum> $enumClass */
                $enumClass = $enumReflection->getName();

                $enumValue = $enumClass::tryFrom($value);

                if ($enumValue instanceof BackedEnum) {
                    return $enumValue;
                }
            }
        }

        return $property->isNullable
            ? null
            : throw new DeserializeException('Could not deserialize BackedEnum');
    }
}
