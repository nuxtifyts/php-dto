<?php

namespace Nuxtifyts\PhpDto\Support;

use Exception;

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
     *
     * @throws Exception
     */
    abstract public function handle(Passable $passable): mixed;
}
