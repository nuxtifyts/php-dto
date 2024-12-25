<?php

namespace Nuxtifyts\PhpDto\Support;

/**
 * @template T of Passable
 */
class Pipeline
{
    /** @var array<array-key, class-string<Pipe<T>>> */
    protected array $pipes = [];

    /**
     * @param class-string<T> $passableClass
     *
     * @phpstan-return self<T>
     */
    public function __construct(
        protected readonly string $passableClass
    ) {
    }

    /**
     * @param class-string<Pipe<T>> $pipe
     */
    public function through(string $pipe): static
    {
        $this->pipes[] = $pipe;

        return $this;
    }

    /**
     * @param T $passable
     *
     * @return T
     */
    public function sendThenReturn(Passable $passable): Passable
    {
        foreach ($this->pipes as $pipe) {
            $passable = new $pipe()->handle($passable);
        }

        return $passable;
    }
}
