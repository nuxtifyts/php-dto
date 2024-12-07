<?php

namespace Nuxtifyts\PhpDto\Normalizers;

use Nuxtifyts\PhpDto\Data;

abstract readonly class Normalizer
{
    /**
     * @param class-string<Data> $class
     */
    final public function __construct(
        protected mixed $value,
        protected string $class,
    ) {}

    /**
     * @return array<string, mixed>|false
     */
    abstract public function normalize(): array|false;
}
