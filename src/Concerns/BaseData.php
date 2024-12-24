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

            return $context->hasComputedProperties
                ? static::instanceWithConstructorCallFrom($context, $value)
                : static::instanceWithoutConstructorFrom($context, $value);
        } catch (Throwable $e) {
            throw new DeserializeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param ClassContext<static> $context
     * @param array<string, mixed> $value
     *
     * @throws Throwable
     */
    protected static function instanceWithoutConstructorFrom(ClassContext $context, array $value): static
    {
        $instance = $context->newInstanceWithoutConstructor();

        foreach ($context->properties as $propertyContext) {
            $propertyName = $propertyContext->propertyName;

            $instance->{$propertyName} = $propertyContext->deserializeFrom($value);
        }

        return $instance;
    }

    /**
     * @param ClassContext<static> $context
     * @param array<string, mixed> $value
     *
     * @throws Throwable
     */
    protected static function instanceWithConstructorCallFrom(ClassContext $context, array $value): static
    {
        /** @var array<string, mixed> $args */
        $args = [];

        foreach ($context->constructorParams as $paramName) {
            $propertyContext = $context->properties[$paramName] ?? null;

            if (!$propertyContext) {
                throw new DeserializeException(
                    "Could not find property context for constructor param: $paramName"
                );
            }

            $args[$paramName] = $propertyContext->deserializeFrom($value);
        }

        $instance = $context->newInstanceWithConstructorCall(...$args);

        if (!$instance instanceof static) {
            throw new DeserializeException('Could not create instance of ' . static::class);
        }

        return $instance;
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
                if ($propertyContext->isComputed) {
                    continue;
                }

                $propertyName = $propertyContext->propertyName;

                $serializableArray[$propertyName] = $propertyContext->serializeFrom($this)[$propertyName];
            }

            return $serializableArray;
        } catch (Throwable $e) {
            throw new SerializeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
