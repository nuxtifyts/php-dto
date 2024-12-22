<?php

namespace Nuxtifyts\PhpDto;

use Nuxtifyts\PhpDto\Contracts\BaseData as BaseDataContract;
use Nuxtifyts\PhpDto\Concerns\BaseData;
use Nuxtifyts\PhpDto\Exceptions\SerializeException;

abstract readonly class Data implements BaseDataContract
{
    use BaseData;

    /**
     * @return array<string, mixed>
     *
     * @throws SerializeException
     */
    final public function toArray(): array
    {
        return $this->jsonSerialize();
    }

    final public function toJson(): false|string
    {
        return json_encode($this);
    }
}
