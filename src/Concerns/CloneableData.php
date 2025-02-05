<?php

namespace Nuxtifyts\PhpDto\Concerns;

use Nuxtifyts\PhpDto\Contexts\ClassContext;
use Nuxtifyts\PhpDto\Exceptions\DataCreationException;
use Nuxtifyts\PhpDto\Normalizers\Concerns\HasNormalizers;
use ReflectionClass;
use Throwable;

trait CloneableData
{
    use HasNormalizers;

    /**
     * @throws DataCreationException
     */
    public function with(mixed ...$args): static
    {
        try {
            if (empty($args)) {
                throw DataCreationException::invalidParamsPassed(static::class);
            }

            /** @var ClassContext<static> $context */
            $context = ClassContext::getInstance(static::class);

            $value = static::normalizeValue($args, static::class, $context->normalizers)
                ?: static::normalizeValue($args[0], static::class, $context->normalizers);

            if ($value === false) {
                throw DataCreationException::invalidParamsPassed(static::class);
            }

            $cloneDataClosure = fn (): static => $context->hasComputedProperties
                ? $this->cloneInstanceWithConstructorCall($context, $value)
                : $this->cloneInstanceWithoutConstructorCall($context, $value);

            return $context->isLazy
                ? $context->newLazyProxy($cloneDataClosure)
                : $cloneDataClosure();
        } catch (Throwable $t) {
            throw DataCreationException::unableToCloneInstanceWithNewData(static::class, $t);
        }
    }

    /**
     * @param ClassContext<static> $context
     * @param array<string, mixed> $value
     *
     * @throws Throwable
     */
    protected function cloneInstanceWithConstructorCall(ClassContext $context, array $value): static
    {
        /** @var array<string, mixed> $args */
        $args = [];

        foreach ($context->constructorParams as $paramName) {
            $propertyContext = $context->properties[$paramName] ?? null;

            if (!$propertyContext) {
                throw DataCreationException::invalidProperty();
            }

            $args[$paramName] = array_key_exists($propertyContext->propertyName, $value)
                ? $value[$paramName]
                : $this->{$propertyContext->propertyName};
        }

        return $context->newInstanceWithConstructorCall(...$args);
    }

    /**
     * @param ClassContext<static> $context
     * @param array<string, mixed> $value
     *
     * @throws Throwable
     */
    protected function cloneInstanceWithoutConstructorCall(ClassContext $context, array $value): static
    {
        $instance = $context->newInstanceWithoutConstructor();

        foreach ($context->properties as $propertyContext) {
            $instance->{$propertyContext->propertyName} =
                array_key_exists($propertyContext->propertyName, $value)
                    ? $value[$propertyContext->propertyName]
                    : $this->{$propertyContext->propertyName};
        }

        return $instance;
    }
}
