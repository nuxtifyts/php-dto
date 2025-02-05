<?php

namespace Nuxtifyts\PhpDto\Concerns;

use Nuxtifyts\PhpDto\Contexts\ClassContext;
use Nuxtifyts\PhpDto\Exceptions\DataCreationException;
use Nuxtifyts\PhpDto\Exceptions\DeserializeException;
use Nuxtifyts\PhpDto\Exceptions\SerializeException;
use Nuxtifyts\PhpDto\Normalizers\Concerns\HasNormalizers;
use Nuxtifyts\PhpDto\Pipelines\DeserializePipeline\DeserializePipeline;
use Nuxtifyts\PhpDto\Pipelines\DeserializePipeline\DeserializePipelinePassable;
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
            /** @var ClassContext<static> $context */
            $context = ClassContext::getInstance(static::class);

            $value = static::normalizeValue($args, static::class, $context->normalizers)
                ?: static::normalizeValue($args[0] ?? [], static::class, $context->normalizers);

            if ($value === false) {
                throw DataCreationException::invalidParamsPassed(static::class);
            }

            $dataCreationClosure = static function () use ($context, $value): static {
                $data = DeserializePipeline::createFromArray()
                    ->sendThenReturn(new DeserializePipelinePassable(
                        classContext: $context,
                        data: $value
                    ))
                    ->data;

                return $context->constructFromArray($data);
            };

            return $context->isLazy
                ? $context->newLazyProxy($dataCreationClosure)
                : $dataCreationClosure();
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
            /** @var ClassContext<static> $context */
            $context = ClassContext::getInstance(static::class);

            $value = static::normalizeValue($value, static::class, $context->normalizers);

            if ($value === false) {
                throw DeserializeException::invalidValue();
            }

            $dataCreationClosure = static function () use ($context, $value): static {
                $data = DeserializePipeline::hydrateFromArray()
                    ->sendThenReturn(new DeserializePipelinePassable(
                        classContext: $context,
                        data: $value
                    ))
                    ->data;

                return $context->hasComputedProperties
                    ? $context->constructFromArray($data)
                    : static::instanceWithoutConstructorFrom($context, $data);
            };

            return $context->isLazy
                ? $context->newLazyProxy($dataCreationClosure)
                : $dataCreationClosure();
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
     * @return array<string, mixed>
     *
     * @throws SerializeException
     */
    final public function jsonSerialize(): array
    {
        try {
            $context = ClassContext::getInstance($this::class);

            $serializedData = [];
            foreach ($context->properties as $propertyContext) {
                if ($propertyContext->isComputed || $propertyContext->isHidden) {
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
