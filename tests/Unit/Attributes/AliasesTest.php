<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Attributes;

use Nuxtifyts\PhpDto\Attributes\Property\Aliases;
use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\Pipelines\DeserializePipeline\ResolveValuesFromAliasesPipe;
use Nuxtifyts\PhpDto\Tests\Dummies\PersonData;
use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use Throwable;

#[CoversClass(PropertyContext::class)]
#[CoversClass(ResolveValuesFromAliasesPipe::class)]
#[CoversClass(Aliases::class)]
#[UsesClass(PersonData::class)]
final class AliasesTest extends UnitCase
{
    /**
     * @throws Throwable
     */
    #[Test]
    public function will_be_able_to_resolve_value_from_aliases(): void
    {
        $person = PersonData::from([
            'first_name' => 'John',
            'last_name' => 'Doe'
        ]);

        self::assertEquals('John', $person->firstName);
        self::assertEquals('Doe', $person->lastName);

        $person = PersonData::from([
            'first_name' => 'John',
            'family_name' => 'Doe'
        ]);

        self::assertEquals('John', $person->firstName);
        self::assertEquals('Doe', $person->lastName);
    }
}
