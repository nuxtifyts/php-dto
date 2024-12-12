<?php

namespace Nuxtifyts\PhpDto\Tests\Dummies;

use Nuxtifyts\PhpDto\Data;
use DateTimeInterface;

final readonly class UserBirthdateData extends Data
{
    public function __construct(
        public string $name,
        public DateTimeInterface $birthdate
    ) {
    }
}
