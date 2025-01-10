<?php

namespace Nuxtifyts\PhpDto\Validation\Rules;

use Nuxtifyts\PhpDto\Exceptions\ValidationRuleException;
use Nuxtifyts\PhpDto\Support\Arr;
use Override;
use Nuxtifyts\PhpDto\Validation\Rules\RegexRule;

class StringRule extends RegexRule
{
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

        if (
            (!is_null($minLen = Arr::getIntegerOrNull($parameters ?? [], 'minLen'))
                && $minLen < 0)
            || (!is_null($maxLen = Arr::getIntegerOrNull($parameters ?? [], 'maxLen'))
                && $maxLen < $minLen)
        ) {
            throw ValidationRuleException::invalidParameters();
        }

        $lengthPattern = '';
        if ($minLen !== null) {
            $lengthPattern .= '.{' . $minLen . ',';
        } else {
            $lengthPattern .= '.{0,';
        }

        if ($maxLen !== null) {
            $lengthPattern .= $maxLen . '}';
        } else {
            $lengthPattern .= '}';
        }

        $instance->pattern = '/^' . $lengthPattern . '$/u';

        return $instance;
    }

    #[Override]
    public function validationMessage(): string
    {
        return 'The :attribute field must be a valid string.';
    }
}
