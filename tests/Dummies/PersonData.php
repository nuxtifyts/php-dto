<?php

namespace Nuxtifyts\PhpDto\Tests\Dummies;

use Nuxtifyts\PhpDto\Tests\Dummies\Normalizers\DummyNormalizer;
use Nuxtifyts\PhpDto\Tests\Dummies\Normalizers\HumanToPersonNormalizer;
use Nuxtifyts\PhpDto\Attributes\Class\WithNormalizer;
use Nuxtifyts\PhpDto\Attributes\Property\Aliases;
use Nuxtifyts\PhpDto\Attributes\Property\Computed;
use Nuxtifyts\PhpDto\Data;

#[WithNormalizer(DummyNormalizer::class)]
#[WithNormalizer(HumanToPersonNormalizer::class)]
final readonly class PersonData extends Data
{
    #[Computed]
    public string $fullName;

    public function __construct(
        #[Aliases('first_name', 'name')]
        public string $firstName,
        #[Aliases('last_name', 'family_name')]
        public string $lastName,
    ) {
        $this->fullName = $this->firstName . ' ' . $this->lastName;
    }
}
