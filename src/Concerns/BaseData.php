<?php

namespace Nuxtifyts\PhpDto\Concerns;

use Nuxtifyts\PhpDto\Exceptions\DeserializeException;
use Nuxtifyts\PhpDto\Exceptions\SerializeException;
use Nuxtifyts\PhpDto\Support\Traits\HasNormalizers;
use Nuxtifyts\PhpDto\Support\Traits\HasSerializers;
use Nuxtifyts\PhpDto\Support\Data\DataCacheHelper;
use ReflectionClass;
use Throwable;

trait BaseData
{
    use HasSerializers;
    use HasNormalizers;

    /**
     * @throws DeserializeException
     */
    final public static function from(mixed $value): static
    {
        try {
            $value = static::normalizeValue($value, static::class);

            if (empty($value)) {
                throw new DeserializeException(
                    code: DeserializeException::INVALID_VALUE_ERROR_CODE
                );
            }

            $reflection = new ReflectionClass(static::class);
            $instance = $reflection->newInstanceWithoutConstructor();

            $cachedSerializers = DataCacheHelper::get(static::class);

            foreach ($reflection->getProperties() as $property) {
                $serializers = $cachedSerializers[$property->getName()] ?? null;

                if (!$serializers) {
                    $serializers = $instance->resolveSerializers($property, $instance);

                    DataCacheHelper::append(
                        static::class,
                        [$property->getName() => $serializers]
                    );
                }

                $propertyDeserialized = false;

                foreach ($serializers as $serializer) {
                    try {
                        $propertyValue = $serializer->deserialize($property, $value);

                        $instance->{$property->getName()} = $propertyValue;

                        $propertyDeserialized = true;

                        break;
                    } catch (DeserializeException) {}
                }

                if (!$propertyDeserialized) {
                    throw new DeserializeException('Could not deserialize value for property: ' . $property->getName());
                }
            }

            return $instance;
        } catch (Throwable $e) {
            throw new DeserializeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @return array<string, mixed>
     *
     * @throws SerializeException
     */
    final public function jsonSerialize(): array
    {
        try {
            $reflection = new ReflectionClass($this);
            $cachedSerializers = DataCacheHelper::get(static::class);

            $serializableArray = [];

            foreach ($reflection->getProperties() as $property) {
                $serializers = $cachedSerializers[$property->getName()] ?? null;

                if (!$serializers) {
                    $serializers = $this->resolveSerializers($property, $this);

                    DataCacheHelper::append(
                        static::class,
                        [$property->getName() => $serializers]
                    );
                }

                $propertySerialized = false;
                foreach ($serializers as $serializer) {
                    try {
                        $propertyValue = $serializer->serialize($property, $this);

                        $serializableArray[$property->getName()] = $propertyValue[$property->getName()];

                        $propertySerialized = true;

                        break;
                    } catch (SerializeException) {}
                }

                if (!$propertySerialized) {
                    throw new SerializeException('Could not serialize property: ' . $property->getName());
                }
            }

            return $serializableArray;
        } catch (Throwable $e) {
            throw new SerializeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
