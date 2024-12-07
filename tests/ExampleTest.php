<?php

namespace Nuxtifyts\PhpDto\Tests;

use Nuxtifyts\PhpDto\Data;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ExampleTest extends TestCase
{
    #[Test]
    public function testExample(): void
    {
        $class = new readonly class ('John') extends Data {
            public function __construct(public string $name) {}
        };

        self::assertEquals('John', $class->name);
    }
}
