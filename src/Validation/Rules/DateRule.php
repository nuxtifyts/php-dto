<?php

namespace Nuxtifyts\PhpDto\Validation\Rules;

use Nuxtifyts\PhpDto\Exceptions\ValidationRuleException;
use Nuxtifyts\PhpDto\Support\Arr;

class DateRule implements ValidationRule
{
    /** 
     *  @var list<string>
     */
    protected array $formats = [];    

    public string $name {
        get {
            return 'date';
        }
    }

    public function evaluate(mixed $value): bool
    {
        return empty($this->formats)
            ? is_string($value) && strtotime($value) !== false
            : is_string($value) && array_any(
                $this->formats,
                static fn (string $format): bool => (bool) date_create_from_format($format, $value)
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

        $formats = Arr::getArray($parameters ?? [], 'formats', []);

        if (array_any(
            $formats,
            static fn (mixed $format): bool => !is_string($format)
        )) {
            throw ValidationRuleException::invalidParameters();
        }

        /** @var array<array-key, string> $formats */
        $instance->formats = array_values($formats);

        return $instance;
    }

    public function validationMessage(): string
    {
        return empty($this->formats) 
            ? 'The :attribute must be a valid date.'
            : 'The :attribute must be a valid date in one of the following formats: ' . implode(', ', $this->formats);
    }
}
