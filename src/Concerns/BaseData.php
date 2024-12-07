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
                $serializer = $cachedSerializers[$property->getName()] ?? null;

                if (!$serializer) {
                    $serializer = $instance->resolveSerializer($property, $instance);

                    DataCacheHelper::append(
                        static::class,
                        [$property->getName() => $serializer]
                    );
                }

                $propertyValue = $serializer->deserialize($property, $value);
                $instance->{$property->getName()} = $propertyValue;
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
                $serializer = $cachedSerializers[$property->getName()] ?? null;

                if (!$serializer) {
                    $serializer = $this->resolveSerializer($property, $this);

                    DataCacheHelper::append(
                        static::class,
                        [$property->getName() => $serializer]
                    );
                }

                foreach ($serializer->serialize($property, $this) as $key => $value) {
                    $serializableArray[$key] = $value;
                }
            }

            return $serializableArray;
        } catch (Throwable $e) {
            throw new SerializeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
