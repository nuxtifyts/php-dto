<?php

namespace Nuxtifyts\PhpDto\Tests\Dummies;

use Nuxtifyts\PhpDto\Data;
use DateTimeImmutable;
use Nuxtifyts\PhpDto\Tests\Dummies\Enums\YesNoBackedEnum;

final readonly class RefundableItemData extends Data
{
    public function __construct(
        public string $id,
        public YesNoBackedEnum $refundable,
        public ?DateTimeImmutable $refundableUntil
    ) {
    }
}
