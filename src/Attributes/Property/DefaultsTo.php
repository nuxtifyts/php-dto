<?php

namespace Nuxtifyts\PhpDto\Attributes\Property;

use Attribute;
use BackedEnum;
use Nuxtifyts\PhpDto\Exceptions\FallbackResolverException;
use Nuxtifyts\PhpDto\FallbackResolver\FallbackResolver;
use ReflectionClass;

#[Attribute(Attribute::TARGET_PROPERTY)]
class DefaultsTo
{
    /** @var array<string, ReflectionClass<object>> */
    protected static array $_resolverReflections = [];

    /** @var ?class-string<FallbackResolver>  */
    protected(set) ?string $fallbackResolverClass = null;

    /**
     * @throws FallbackResolverException
     */
    public function __construct(
        protected(set) BackedEnum|int|string|float|bool|null $value
    ) {
        if (is_string($value) && class_exists($value)) {
            /** @var ReflectionClass<object> $reflection */
            $reflection = self::$_resolverReflections[$value] ??= new ReflectionClass($value);

            if (!$reflection->implementsInterface(FallbackResolver::class)) {
                throw FallbackResolverException::unableToFindResolverClass($value);
            } else {
                /** @var class-string<FallbackResolver> $value */
                $this->fallbackResolverClass = $value;
            }
        }
    }
}
