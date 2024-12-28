<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Serializers;

use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\Contexts\TypeContext;
use Nuxtifyts\PhpDto\Serializers\MixedDataSerializer;
use Nuxtifyts\PhpDto\Serializers\Serializer;
use Nuxtifyts\PhpDto\Tests\Unit\UnitCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Serializer::class)]
#[CoversClass(MixedDataSerializer::class)]
#[CoversClass(PropertyContext::class)]
#[CoversClass(TypeContext::class)]
final class MixedDataSerializerTest extends UnitCase
{

}
