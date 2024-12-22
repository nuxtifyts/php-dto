<?php

namespace Nuxtifyts\PhpDto\Concerns;

use Nuxtifyts\PhpDto\Contexts\ClassContext;
use Nuxtifyts\PhpDto\Exceptions\DeserializeException;
use Nuxtifyts\PhpDto\Exceptions\SerializeException;
use Nuxtifyts\PhpDto\Support\Traits\HasNormalizers;
use ReflectionClass;
use Throwable;

trait BaseData
{
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

            /** @var ClassContext<static> $context */
            $context = ClassContext::getInstance(new ReflectionClass(static::class));
            $instance = $context->newInstanceWithoutConstructor();

            foreach ($context->properties as $propertyContext) {
                $serializers = $propertyContext->serializers();

                if (!$serializers) {
                    throw new DeserializeException(
                        code: DeserializeException::NO_SERIALIZERS_ERROR_CODE
                    );
                }

                $propertyName = $propertyContext->propertyName;
                $propertyDeserialized = false;
                foreach ($serializers as $serializer) {
                    try {
                        $propertyValue = $serializer->deserialize($propertyContext, $value);

                        $instance->{$propertyName} = $propertyValue;

                        $propertyDeserialized = true;

                        break;
                    } catch (DeserializeException) {}
                }

                if (!$propertyDeserialized) {
                    throw new DeserializeException("Could not deserialize value for property: $propertyName");
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
            $context = ClassContext::getInstance(new ReflectionClass($this));

            $serializableArray = [];

            foreach ($context->properties as $propertyContext) {
                $serializers = $propertyContext->serializers();

                if (!$serializers) {
                    throw new SerializeException(
                        code: SerializeException::NO_SERIALIZERS_ERROR_CODE
                    );
                }

                $propertyName = $propertyContext->propertyName;
                $propertySerialized = false;
                foreach ($serializers as $serializer) {
                    try {
                        $propertyValue = $serializer->serialize($propertyContext, $this);

                        $serializableArray[$propertyName] = $propertyValue[$propertyName];

                        $propertySerialized = true;

                        break;
                    } catch (SerializeException) {}
                }

                if (!$propertySerialized) {
                    throw new SerializeException(
                        "Could not serialize property: $propertyName",
                    );
                }
            }

            return $serializableArray;
        } catch (Throwable $e) {
            throw new SerializeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
