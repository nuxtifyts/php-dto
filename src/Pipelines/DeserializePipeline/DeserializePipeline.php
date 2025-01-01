<?php

namespace Nuxtifyts\PhpDto\Pipelines\DeserializePipeline;

use Nuxtifyts\PhpDto\Pipelines\DeserializePipeline\Pipes\DecipherDataPipe;
use Nuxtifyts\PhpDto\Pipelines\DeserializePipeline\Pipes\MapNamesPipe;
use Nuxtifyts\PhpDto\Pipelines\DeserializePipeline\Pipes\RefineDataPipe;
use Nuxtifyts\PhpDto\Pipelines\DeserializePipeline\Pipes\ResolveDefaultDataPipe;
use Nuxtifyts\PhpDto\Pipelines\DeserializePipeline\Pipes\ResolveValuesFromAliasesPipe;
use Nuxtifyts\PhpDto\Support\Pipeline;

/**
 * @extends Pipeline<DeserializePipelinePassable>
 */
class DeserializePipeline extends Pipeline
{
    public static function hydrateFromArray(): self
    {
        return new DeserializePipeline(DeserializePipelinePassable::class)
            ->through(ResolveValuesFromAliasesPipe::class)
            ->through(MapNamesPipe::class)
            ->through(RefineDataPipe::class)
            ->through(DecipherDataPipe::class)
            ->through(ResolveDefaultDataPipe::class);
    }

    /**
     * @desc Basically the same as hydrateFromArray, but without deciphering data.
     * This is used when create a new instance using the `create` static method from `BaseData`.
     */
    public static function createFromArray(): self
    {
        return new DeserializePipeline(DeserializePipelinePassable::class)
            ->through(ResolveValuesFromAliasesPipe::class)
            ->through(MapNamesPipe::class)
            ->through(RefineDataPipe::class)
            ->through(ResolveDefaultDataPipe::class);
    }
}
