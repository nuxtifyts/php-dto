<?php

namespace Nuxtifyts\PhpDto\Validation\Rules;

use Nuxtifyts\PhpDto\Exceptions\ValidationRuleException;
use Nuxtifyts\PhpDto\Support\Arr;
use Override;
use Nuxtifyts\PhpDto\Validation\Rules\RegexRule;

class StringRule extends RegexRule
{
    use Concerns\MinMaxValues;

    public string $name {
        get {
            return 'string';
        }
    }
    
    /** 
     * @param ?array<string, mixed> $parameters
     * 
     * @throws ValidationRuleException
     */
    #[Override]
    public static function make(?array $parameters = null): self
    {
        $instance = new self();

        [$minLen, $maxLen] = self::getMinMaxValues($parameters, 'minLen', 'maxLen');

        $lengthPattern = is_null($minLen) ? '.{0,' : '.{' . $minLen . ',';
        $lengthPattern .= is_null($maxLen) ? '}' : $maxLen . '}';

        $instance->pattern = '/^' . $lengthPattern . '$/u';

        return $instance;
    }

    #[Override]
    public function validationMessage(): string
    {
        return 'The :attribute field must be a valid string.';
    }
}
