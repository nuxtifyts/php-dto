<?php

namespace Nuxtifyts\PhpDto\Tests\Dummies;

use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Tests\Dummies\Enums\YesNoBackedEnum;

final readonly class InvitationData extends Data
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public YesNoBackedEnum $isComing
    ) {
    }
}
