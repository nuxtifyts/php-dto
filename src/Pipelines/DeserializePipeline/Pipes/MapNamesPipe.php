<?php

namespace Nuxtifyts\PhpDto\Pipelines\DeserializePipeline\Pipes;

use Nuxtifyts\PhpDto\Pipelines\DeserializePipeline\DeserializePipelinePassable;
use Nuxtifyts\PhpDto\Support\Passable;
use Nuxtifyts\PhpDto\Support\Pipe;

/**
 * @extends Pipe<DeserializePipelinePassable>
 */
readonly class MapNamesPipe extends Pipe
{
    public function handle(Passable $passable): DeserializePipelinePassable
    {
        if (!$passable->classContext->nameMapperConfig) {
            return $passable;
        }

        $data = $passable->data;

        foreach ($data as $key => $value) {
            $newKey = $passable->classContext->nameMapperConfig->transform($key);

            if ($newKey === false || $newKey === $key) {
                continue;
            }

            $data[$newKey] = $value;
            unset($data[$key]);
        }

        return $passable->with(data: $data);
    }
}
