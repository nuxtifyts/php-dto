<?php

namespace Nuxtifyts\PhpDto\Tests\Dummies\DocsDummies;

use Nuxtifyts\PhpDto\Data;

final readonly class UserDetailsData extends Data
{
    public string $fullName;

    public function __construct(
        public string $firstName,
        public string $lastName
    ) {
        $this->fullName = $this->firstName . ' ' . $this->lastName;
    }
}
