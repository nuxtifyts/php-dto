<?php

namespace Nuxtifyts\PhpDto\Support\Validation\Contracts;

use Throwable;

interface DataValidator
{
    /**
     * @param array<string, mixed> $rules
     */
    public static function createInstance(array $rules): self;

    /**
     * @param array<array-key, mixed> $value
     *
     * @return array<array-key, mixed>
     *
     * @throws Throwable
     */
    public static function validate(array $value): array;
}
