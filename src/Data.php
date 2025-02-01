<?php

namespace Nuxtifyts\PhpDto;

use Nuxtifyts\PhpDto\Contracts\BaseData as BaseDataContract;
use Nuxtifyts\PhpDto\Contracts\EmptyData as EmptyDataContract;
use Nuxtifyts\PhpDto\Contracts\CloneableData as CloneableDataContract;
use Nuxtifyts\PhpDto\Contracts\ValidateableData as ValidateableDataContract;
use Nuxtifyts\PhpDto\Concerns\BaseData;
use Nuxtifyts\PhpDto\Concerns\EmptyData;
use Nuxtifyts\PhpDto\Concerns\CloneableData;
use Nuxtifyts\PhpDto\Concerns\ValidateableData;

abstract readonly class Data implements
    BaseDataContract,
    EmptyDataContract,
    CloneableDataContract,
    ValidateableDataContract
{
    use BaseData;
    use EmptyData;
    use CloneableData;
    use ValidateableData;
}
