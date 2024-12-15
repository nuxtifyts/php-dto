<?php

namespace Nuxtifyts\PhpDto\Tests\Dummies;

final readonly class UserLocationData extends UserData
{
    public function __construct(
        string $firstName,
        string $lastName,
        public AddressData $address
    ) {
        parent::__construct($firstName, $lastName);
    }
}
