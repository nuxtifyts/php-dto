<?php

namespace Nuxtifyts\PhpDto\Concerns;

use Nuxtifyts\PhpDto\Contexts\ClassContext;
use Nuxtifyts\PhpDto\Exceptions\DataCreationException;
use Nuxtifyts\PhpDto\Exceptions\DeserializeException;
use Nuxtifyts\PhpDto\Exceptions\SerializeException;
use Nuxtifyts\PhpDto\Normalizers\Concerns\HasNormalizers;
use Nuxtifyts\PhpDto\Pipelines\DeserializePipeline\DeserializePipeline;
use Nuxtifyts\PhpDto\Pipelines\DeserializePipeline\DeserializePipelinePassable;
use ReflectionClass;
use Throwable;

trait BaseData
{
    use HasNormalizers;

    /**
     * @throws DataCreationException
     */
    final public static function create(mixed ...$args): static
    {
        try {
            $value = static::normalizeValue($args, static::class)
                ?: static::normalizeValue($args[0] ?? [], static::class);

            if ($value === false) {
                throw DataCreationException::invalidParamsPassed(static::class);
            }

            /** @var ClassContext<static> $context */
            $context = ClassContext::getInstance(new ReflectionClass(static::class));

            $data = DeserializePipeline::createFromArray()
                ->sendThenReturn(new DeserializePipelinePassable(
                    classContext: $context,
                    data: $value
                ))
                ->data;

            return static::instanceWithConstructorCallFrom($context, $data);
        } catch (Throwable $e) {
            throw DataCreationException::unableToCreateInstance(static::class, $e);
        }
    }

    /**
     * @throws DeserializeException
     */
    final public static function from(mixed $value): static
    {
        try {
            $value = static::normalizeValue($value, static::class);

            if ($value === false) {
                throw DeserializeException::invalidValue();
            }

            /** @var ClassContext<static> $context */
            $context = ClassContext::getInstance(new ReflectionClass(static::class));

            $data = DeserializePipeline::hydrateFromArray()
                ->sendThenReturn(new DeserializePipelinePassable(
                    classContext: $context,
                    data: $value
                ))
                ->data;

            return $context->hasComputedProperties
                ? static::instanceWithConstructorCallFrom($context, $data)
                : static::instanceWithoutConstructorFrom($context, $data);
        } catch (Throwable $e) {
            throw DeserializeException::generic($e);
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
                throw DeserializeException::invalidParamsPassed();
            }

            $args[$paramName] = $propertyContext->deserializeFrom($value);
        }

        return $context->newInstanceWithConstructorCall(...$args);
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

            $serializedData = [];
            foreach ($context->properties as $propertyContext) {
                if ($propertyContext->isComputed) {
                    continue;
                }

                $propertyName = $propertyContext->propertyName;

                $serializedData[$propertyName] = $propertyContext->serializeFrom($this)[$propertyName];
            }

            return $serializedData;
        } catch (Throwable $e) {
            throw SerializeException::generic($e);
        }
    }

    /**
     * @throws SerializeException
     */
    final public function toArray(): array
    {
        return $this->jsonSerialize();
    }

    final public function toJson(): false|string
    {
        return json_encode($this);
    }
}
