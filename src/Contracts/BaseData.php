<?php

namespace Nuxtifyts\PhpDto\Contracts;

use Nuxtifyts\PhpDto\Exceptions\DeserializeException;
use Nuxtifyts\PhpDto\Exceptions\SerializeException;
use JsonSerializable;

interface BaseData extends JsonSerializable
{
    /**
     * @return array<string, mixed>
     *
     * @throws SerializeException
     */
    public function jsonSerialize(): array;

    /**
     * @throws DeserializeException
     */
    public static function from(mixed $value): static;

    // public static function collect(mixed $value): array;
    // public static function pipeline(): DataPipeline;
}
