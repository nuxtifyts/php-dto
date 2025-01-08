<?php

namespace Nuxtifyts\PhpDto\Validation\Rules;

use BackedEnum;
use Nuxtifyts\PhpDto\Exceptions\ValidationRuleException;
use Nuxtifyts\PhpDto\Support\Arr;

class BackedEnumRule implements ValidationRule
{
    /** @var class-string<BackedEnum> */
    public protected(set) string $backedEnumClass;

    /** 
     *  @var ?array<array-key, BackedEnum>
     */
    public protected(set) ?array $allowedValues = null;

    public string $name {
        get {
            return 'backed_enum';
        }
    }

    public function evaluate(mixed $value): bool
    {
        if ($value instanceof $this->backedEnumClass) {
            /** @var BackedEnum $value */
            $resolvedValue = $value;
        } else if (is_string($value) || is_integer($value)) {
            $resolvedValue = $this->backedEnumClass::tryFrom($value);
        } else {
            return false;
        }

        return !!$resolvedValue
            && (
                is_null($this->allowedValues) 
                || in_array($resolvedValue->value, array_column($this->allowedValues, 'value'))
            );
    }

    /** 
     * @param ?array<string, mixed> $parameters
     *
     * @throws ValidationRuleException
     */
    public static function make(?array $parameters = null): self
    {
        $instance = new self();

        $backedEnumClass = $parameters['backedEnumClass'] ?? null;

        if (
            !is_string($backedEnumClass) 
            || !enum_exists($backedEnumClass) 
            || !is_subclass_of($backedEnumClass, BackedEnum::class)
        ) { 
            throw ValidationRuleException::invalidParameters();
        }

        $instance->backedEnumClass = $backedEnumClass;
        $instance->allowedValues = array_filter(
            array_map(static fn (mixed $value) =>
                ($value instanceof $instance->backedEnumClass)
                    ? $value
                    : null,
                Arr::getArray($parameters ?? [], 'allowedValues')
            )
        ) ?: null;

        return $instance;
    }

    public function validationMessage(): string
    {
        if ($this->allowedValues) {
            $allowedValues = implode(
                ', ', 
                array_map(static fn (BackedEnum $value) => $value->value, $this->allowedValues)
            );

            return "The :attribute field must be one of the following values: $allowedValues.";
        } else {
            return 'The :attribute field is invalid.';
        }
    }
}
