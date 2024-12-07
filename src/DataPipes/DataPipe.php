<?php

namespace Nuxtifyts\PhpDto\DataPipes;

interface DataPipe
{
    /**
     * @param $next callable($mixed $payload): array<array-key, mixed>
     *
     * @return array<array-key, mixed>
     */
    public function handle(
        mixed $payload,
        callable $next
    ): array;
}
