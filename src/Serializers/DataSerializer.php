<?php

namespace Nuxtifyts\PhpDto\Serializers;

use ArrayAccess;
use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\Enums\Property\Type;
use Nuxtifyts\PhpDto\Contracts\BaseData as BaseDataContract;
use Nuxtifyts\PhpDto\Exceptions\DeserializeException;
use Nuxtifyts\PhpDto\Exceptions\SerializeException;
use Exception;

class DataSerializer extends Serializer
{
    /**
     * @inheritDoc
     */
    public static function supportedTypes(): array
    {
        return [
            Type::DATA
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
                $value instanceof BaseDataContract => $value->jsonSerialize(),
                $value === null && $property->isNullable => null,
                default => throw new SerializeException('Value is not an instance of BaseDataContract')
            }
        ];
    }

    /**
     * @inheritDoc
     */
    public function deserialize(PropertyContext $property, ArrayAccess|array $data): ?BaseDataContract
    {
        $value = $data[$property->propertyName] ?? null;

        if (is_array($value)) {
            try {
                foreach ($property->dataReflections as $dataReflection) {
                    // @phpstan-ignore-next-line
                    $value = call_user_func( [$dataReflection->getName(), 'from'], $value);

                    if (!$value instanceof BaseDataContract) {
                        throw new Exception('Not an instance of BaseDataContract');
                    }

                    return $value;
                }
            // @codeCoverageIgnoreStart
            } catch (Exception) {}
            // @codeCoverageIgnoreEnd
        }

        return is_null($value) && $property->isNullable
            ? null
            : throw new DeserializeException('Value is not an instance of BaseDataContract');
    }
}
