<?php

namespace Nuxtifyts\PhpDto\Pipelines\DeserializePipeline;

use Nuxtifyts\PhpDto\Support\Passable;
use Nuxtifyts\PhpDto\Support\Pipe;
use Exception;

/**
 * @extends Pipe<DeserializePipelinePassable>
 */
readonly class RefineDataPipe extends Pipe
{
    public function handle(Passable $passable): DeserializePipelinePassable
    {
        $data = $passable->data;

        foreach ($passable->classContext->properties as $propertyContext) {
            $propertyName = $propertyContext->propertyName;

            if (!array_key_exists($propertyName, $data)) {
                continue;
            }

            foreach ($propertyContext->dataRefiners as $dataRefiner) {
                try {
                    $data[$propertyName] = $dataRefiner->refine(
                        value: $data[$propertyName],
                        property: $propertyContext
                    );
                } catch (Exception) {}
            }
        }

        return $passable->with($data);
    }
}
