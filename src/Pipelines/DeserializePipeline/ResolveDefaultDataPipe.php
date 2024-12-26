<?php

namespace Nuxtifyts\PhpDto\Pipelines\DeserializePipeline;

use Nuxtifyts\PhpDto\Support\Passable;
use Nuxtifyts\PhpDto\Support\Pipe;

/**
 * @extends Pipe<DeserializePipelinePassable>
 */
readonly class ResolveDefaultDataPipe extends Pipe
{
    public function handle(Passable $passable): DeserializePipelinePassable
    {
        $data = $passable->data;

        foreach ($passable->classContext->properties as $propertyContext) {
            if (array_key_exists($propertyContext->propertyName, $data)) {
                continue;
            }

            if ($propertyContext->fallbackConfig) {
                $data[$propertyContext->propertyName] = $propertyContext->fallbackConfig->resolverClass
                    ? $propertyContext->fallbackConfig->resolverClass::resolve($data, $propertyContext)
                    : $propertyContext->fallbackConfig->value;
            }

            if ($propertyContext->reflection->hasDefaultValue()) {
                $data[$propertyContext->propertyName] = $propertyContext->reflection->getDefaultValue();
            }
        }

        return $passable->with(data: $data);
    }
}
