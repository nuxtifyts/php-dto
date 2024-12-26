<?php

namespace Nuxtifyts\PhpDto\Pipelines\DeserializePipeline;

use Nuxtifyts\PhpDto\Contexts\ClassContext;
use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Support\Passable;

readonly class DeserializePipelinePassable extends Passable
{
    /**
     * @template T of Data
     *
     * @param ClassContext<T> $classContext
     * @param array<string, mixed> $data
     */
    public function __construct(
        protected(set) ClassContext $classContext,
        protected(set) array $data
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public function with(array $data): self
    {
        return new self($this->classContext, $data);
    }
}
