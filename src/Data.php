<?php

namespace Nuxtifyts\PhpDto;

use Nuxtifyts\PhpDto\Contracts\BaseData as BaseDataContract;
use Nuxtifyts\PhpDto\Contracts\EmptyData as EmptyDataContract;
use Nuxtifyts\PhpDto\Contracts\CloneableData as CloneableDataContract;
use Nuxtifyts\PhpDto\Contracts\LazyData as LazyDataContract;
use Nuxtifyts\PhpDto\Concerns\BaseData;
use Nuxtifyts\PhpDto\Concerns\EmptyData;
use Nuxtifyts\PhpDto\Concerns\CloneableData;
use Nuxtifyts\PhpDto\Concerns\LazyData;

abstract readonly class Data implements
    BaseDataContract,
    EmptyDataContract,
    CloneableDataContract,
    LazyDataContract
{
    use BaseData;
    use EmptyData;
    use CloneableData;
    use LazyData;
}
