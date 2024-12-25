<?php

namespace Nuxtifyts\PhpDto\Pipelines\DeserializePipeline;

use Nuxtifyts\PhpDto\Support\Passable;
use Nuxtifyts\PhpDto\Support\Pipe;

/**
 * @extends Pipe<DeserializePipelinePassable>
 */
readonly class ResolveValuesFromAliasesPipe extends Pipe
{
    public function handle(Passable $passable): DeserializePipelinePassable
    {
        $data = $passable->data;

        foreach ($passable->classContext->properties as $propertyContext) {
            $propertyName = $propertyContext->propertyName;

            if (array_key_exists($propertyName, $data)) {
                continue;
            }

            $aliases = $propertyContext->aliases;

            foreach ($aliases as $alias) {
                if (array_key_exists($alias, $data)) {
                    $data[$propertyName] = $data[$alias];
                    break;
                }
            }
        }

        return $passable->with(data: $data);
    }
}
