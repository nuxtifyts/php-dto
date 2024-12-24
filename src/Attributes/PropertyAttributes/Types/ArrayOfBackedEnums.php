<?php

namespace Nuxtifyts\PhpDto\Attributes\PropertyAttributes\Types;

use Attribute;
use BackedEnum;
use InvalidArgumentException;
use ReflectionEnum;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class ArrayOfBackedEnums
{
    /** @var array<string, ReflectionEnum<BackedEnum>> */
    private static array $_enumReflections = [];

    /** @var list<class-string<BackedEnum>> $enums */
    private(set) array $enums;

    /** @var array<string, ReflectionEnum<BackedEnum>> */
    private(set) array $resolvedBackedEnumReflections = [];

    /**
     * @param class-string<BackedEnum>|list<class-string<BackedEnum>> $enums
     */
    public function __construct(
        string|array $enums
    ) {
        $enumArr = is_array($enums) ? $enums : [$enums];

        if (empty($enumArr)) {
            throw new InvalidArgumentException(
                'BackedEnumArray must have at least one enum'
            );
        } else {
            foreach ($enumArr as $enum) {
                if (!enum_exists($enum)) {
                    throw new InvalidArgumentException(
                        'Invalid enum passed to BackedEnumArray: ' . $enum
                    );
                }

                /**
                 * @var ReflectionEnum<BackedEnum> $reflectionEnum
                 */
                $reflectionEnum = self::$_enumReflections[$enum] ??= new ReflectionEnum($enum);

                if (!$reflectionEnum->isBacked()) {
                    throw new InvalidArgumentException(
                        'Non-backed enum passed to BackedEnumArray: ' . $enum
                    );
                }

                $this->resolvedBackedEnumReflections[$enum] = $reflectionEnum;
            }
        }

        $this->enums = is_array($enums) ? $enums : [$enums];
    }
}
