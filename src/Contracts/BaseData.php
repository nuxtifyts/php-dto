<?php

namespace Nuxtifyts\PhpDto\Contracts;

use Nuxtifyts\PhpDto\Exceptions\DataCreationException;
use Nuxtifyts\PhpDto\Exceptions\DeserializeException;
use Nuxtifyts\PhpDto\Exceptions\SerializeException;
use JsonSerializable;

interface BaseData extends JsonSerializable
{
    /**
     * @throws DataCreationException
     */
    public static function create(mixed ...$args): static;

    /**
     * @return array<string, mixed>
     *
     * @throws SerializeException
     */
    public function jsonSerialize(): array;

    /**
     * @return array<string, mixed>
     *
     * @throws SerializeException
     */
    public function toArray(): array;

    /**
     * @return false|string
     *
     * @throws SerializeException
     */
    public function toJson(): false|string;

    /**
     * @throws DeserializeException
     */
    public static function from(mixed $value): static;
}
