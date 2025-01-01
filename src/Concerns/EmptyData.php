<?php

namespace Nuxtifyts\PhpDto\Concerns;

use Nuxtifyts\PhpDto\Contexts\ClassContext;
use Nuxtifyts\PhpDto\Exceptions\DataCreationException;
use ReflectionClass;
use Throwable;

trait EmptyData
{
    /**
     * @throws DataCreationException
     */
    public static function empty(): static
    {
        try {
            /** @var ClassContext<static> $classContext */
            $classContext = ClassContext::getInstance(static::class);

            return $classContext->emptyValue();
        } catch (Throwable $t) {
            throw DataCreationException::unableToCreateEmptyInstance(static::class, $t);
        }
    }
}
