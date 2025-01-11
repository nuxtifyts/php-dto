<?php

namespace Nuxtifyts\PhpDto\Validation\Rules;

use Nuxtifyts\PhpDto\Enums\Property\Type;
use Nuxtifyts\PhpDto\Exceptions\ValidationRuleException;
use Nuxtifyts\PhpDto\Support\Arr;
use Override;

class NumericRule implements ValidationRule
{
    use Concerns\MinMaxValues;

    /** @var Type::INT | Type::FLOAT */
    protected Type $type;

    protected int|float|null $min = null;
    
    protected int|float|null $max = null;

    public string $name {
        get {
            return 'numeric';
        }
    }

    public function evaluate(mixed $value): bool
    {
        if (
            ($this->type === Type::INT && !is_int($value))
            || ($this->type === Type::FLOAT && !is_float($value))
            || (!is_null($this->min) && $value < $this->min)
            || (!is_null($this->max) && $value > $this->max)
        ) {
            return false;
        }

        return true;
    }

    /** 
     * @param ?array<string, mixed> $parameters
     * 
     * @throws ValidationRuleException
     */
    public static function make(?array $parameters = null): self
    {
        $instance = new self();

        $numericType = Arr::getBackedEnum(
            $parameters ?? [], 
            'type',
            Type::class,
            Type::INT
        );

        if (!in_array(
            $numericType->value,
            array_column(Type::NUMERIC_TYPES, 'value'))
        ) {
            throw ValidationRuleException::invalidParameters();
        }
        
        /** @var Type::INT | Type::FLOAT $numericType */
        $instance->type = $numericType;

        [$instance->min, $instance->max] = self::getMinMaxValues($parameters, type: $numericType);

        return $instance;
    }

    public function validationMessage(): string
    {
        return match($this->type) {
            Type::INT => 'The :attribute field must be a valid integer.',
            Type::FLOAT => 'The :attribute field must be a valid float.',
        };
    }
}
