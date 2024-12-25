<?php

namespace Nuxtifyts\PhpDto\Support;

/**
 * @template T of Passable
 */
abstract readonly class Pipe
{
    final public function __construct() {}

    /**
     * @param T $passable
     *
     * @return T
     */
    abstract public function handle(Passable $passable): mixed;
}
