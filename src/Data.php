<?php

namespace Nuxtifyts\PhpDto;

use Nuxtifyts\PhpDto\Contracts\BaseData as BaseDataContract;
use Nuxtifyts\PhpDto\Contracts\EmptyData as EmptyDataContract;
use Nuxtifyts\PhpDto\Concerns\BaseData;
use Nuxtifyts\PhpDto\Concerns\EmptyData;

abstract readonly class Data implements
    BaseDataContract,
    EmptyDataContract
{
    use BaseData;
    use EmptyData;
}
