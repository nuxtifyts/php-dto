<?php

namespace Nuxtifyts\PhpDto\Pipelines\DeserializePipeline;

use Nuxtifyts\PhpDto\Support\Passable;
use Nuxtifyts\PhpDto\Support\Pipe;

/**
 * @extends Pipe<DeserializePipelinePassable>
 */
readonly class DecipherDataPipe extends Pipe
{
    public function handle(Passable $passable): DeserializePipelinePassable
    {
        $data = $passable->data;

        foreach ($passable->classContext->properties as $propertyContext) {
            $propertyName = $propertyContext->propertyName;

            if (
                !$propertyContext->cipherConfig
                || !array_key_exists($propertyName, $data)
                || !is_string($data[$propertyName])
            ) {
                continue;
            }

            $data[$propertyName] = $propertyContext->cipherConfig->dataCipherClass::decipher(
                data: $data[$propertyName],
                secret: $propertyContext->cipherConfig->secret,
                decode: $propertyContext->cipherConfig->encoded
            );
        }

        return $passable->with(data: $data);
    }
}
