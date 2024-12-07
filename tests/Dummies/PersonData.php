<?php

namespace Nuxtifyts\PhpDto\Tests\Dummies;

use Nuxtifyts\PhpDto\Data;

final readonly class PersonData extends Data
{
    public string $fullName;

    public function __construct(
        public string $firstName,
        public string $lastName,
    ) {
        $this->fullName = $this->firstName . ' ' . $this->lastName;
    }
}
