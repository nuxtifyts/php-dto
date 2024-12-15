<?php

namespace Nuxtifyts\PhpDto\Tests\Dummies;

use Nuxtifyts\PhpDto\Data;

readonly class UserData extends Data
{
    public function __construct(
        public string $firstName,
        public string $lastName
    ) {
    }
}
