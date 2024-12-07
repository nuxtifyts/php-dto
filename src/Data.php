<?php

namespace Nuxtifyts\PhpDto;

use Nuxtifyts\PhpDto\Contracts\BaseData as BaseDataContract;
use Nuxtifyts\PhpDto\Concerns\BaseData;

abstract readonly class Data implements BaseDataContract
{
    use BaseData;
}
