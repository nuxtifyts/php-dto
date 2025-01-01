<?php

namespace Nuxtifyts\PhpDto\Tests\Dummies\NonData;

class Human
{
    public function __construct(
        public string $name,
        public string $surname
    ) {
    }
}
