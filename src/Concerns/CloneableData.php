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

            $value = static::normalizeValue($args, static::class)
                ?: static::normalizeValue($args[0], static::class);

            if ($value === false) {
                throw DataCreationException::invalidParamsPassed(static::class);
            }

            /** @var ClassContext<static> $context */
            $context = ClassContext::getInstance(new ReflectionClass(static::class));

            return $context->hasComputedProperties
                ? $this->cloneInstanceWithConstructorCall($context, $value)
                : $this->cloneInstanceWithoutConstructorCall($context, $value);
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
