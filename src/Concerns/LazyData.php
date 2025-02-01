<?php

namespace Nuxtifyts\PhpDto\Concerns;

use Nuxtifyts\PhpDto\Contexts\ClassContext;
use Nuxtifyts\PhpDto\Exceptions\DataCreationException;
use Nuxtifyts\PhpDto\Normalizers\Concerns\HasNormalizers;
use Nuxtifyts\PhpDto\Pipelines\DeserializePipeline\DeserializePipeline;
use Nuxtifyts\PhpDto\Pipelines\DeserializePipeline\DeserializePipelinePassable;
use Throwable;

trait LazyData
{
    use HasNormalizers;

    /**
     * @throws DataCreationException
     */
    public static function createLazy(mixed ...$args): static
    {
        try {
            /** @var ClassContext<static> $context */
            $context = ClassContext::getInstance(static::class);

            $value = static::normalizeValue($args, static::class, $context->normalizers)
                ?: static::normalizeValue($args[0] ?? [], static::class, $context->normalizers);

            if ($value === false) {
                throw DataCreationException::invalidParamsPassed(static::class);
            }

            return $context->newLazyProxy(
                static function () use($context, $value): static {
                    $data = DeserializePipeline::createFromArray()
                        ->sendThenReturn(new DeserializePipelinePassable(
                            classContext: $context,
                            data: $value
                        ))
                        ->data;

                    return $context->constructFromArray($data);
                }
            );
        } catch (Throwable $e) {
            throw DataCreationException::unableToCreateLazyInstance(static::class, $e);
        }
    }

    /**
     * @param callable(static $data): static $callable
     *
     * @throws DataCreationException
     */
    public static function createLazyUsing(callable $callable): static
    {
        try {
            /** @var ClassContext<static> $context */
            $context = ClassContext::getInstance(static::class);

            return $context->newLazyProxy($callable);
            // @codeCoverageIgnoreStart
        } catch (Throwable $e) {
            throw DataCreationException::unableToCreateLazyInstance(static::class, $e);
        }
        // @codeCoverageIgnoreEnd
    }
}
