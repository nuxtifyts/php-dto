<?php

namespace Nuxtifyts\PhpDto\Tests\Dummies;

use Nuxtifyts\PhpDto\Data;

final readonly class AddressData extends Data
{
    public function __construct(
        public string $street,
        public string $city,
        public string $state,
        public string $zip,
        public CountryData $country,
        public ?CoordinatesData $coordinates
    ) {
    }
}
