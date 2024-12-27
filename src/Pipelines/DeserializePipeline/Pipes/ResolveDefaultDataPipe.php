<?php

namespace Nuxtifyts\PhpDto\Pipelines\DeserializePipeline\Pipes;

use Nuxtifyts\PhpDto\Pipelines\DeserializePipeline\DeserializePipelinePassable;
use Nuxtifyts\PhpDto\Support\Passable;
use Nuxtifyts\PhpDto\Support\Pipe;
use ReflectionParameter;

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

            $constructorParameters = $propertyContext->reflection
                ->getDeclaringClass()
                ->getConstructor()
                ?->getParameters();

            if (
                $propertyParameter = array_find(
                    $constructorParameters ?? [],
                    fn (ReflectionParameter $parameter) => $parameter->getName() === $propertyContext->propertyName
                )
            ) {
                /** @var ReflectionParameter $propertyParameter */
                if ($propertyParameter->isDefaultValueAvailable()) {
                    $data[$propertyContext->propertyName] = $propertyParameter->getDefaultValue();
                }
            }
        }

        return $passable->with(data: $data);
    }
}
