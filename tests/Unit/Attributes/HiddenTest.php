<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Attributes;

use Nuxtifyts\PhpDto\Attributes\Property\Hidden;
use Nuxtifyts\PhpDto\Data;
use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Throwable;

#[CoversClass(Hidden::class)]
final class HiddenTest extends UnitCase
{
    /**
     * @throws Throwable
     */
    #[Test]
    public function test_it_can_hide_a_property(): void
    {
        $object = new readonly class ('Something that should be hidden') extends Data {
              public function __construct(
                  #[Hidden]
                  public string $hidden
              ) {
              }
        };

        self::assertEquals([], $object->toArray());
    }
}
