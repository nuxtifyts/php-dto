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
     * @param BackedEnum|array<array-key, mixed>|int|string|float|bool|null $value
     *
     * @throws FallbackResolverException
     */
    public function __construct(
        protected(set) BackedEnum|array|int|string|float|bool|null $value
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
