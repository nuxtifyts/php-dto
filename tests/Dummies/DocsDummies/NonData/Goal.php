<?php

namespace Nuxtifyts\PhpDto\Tests\Dummies\DocsDummies\NonData;

use DateTimeImmutable;

class Goal
{
    public function __construct(
        public string $summary,
        public string $description,
        public DateTimeImmutable $dueDate
    ) {
    }
}
