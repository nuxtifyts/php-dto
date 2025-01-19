<?php

namespace Nuxtifyts\PhpDto\Validation\Rules;

use Nuxtifyts\PhpDto\Exceptions\ValidationRuleException;
use Nuxtifyts\PhpDto\Support\Arr;
use Override;

class StringRule extends RegexRule
{
    use Concerns\MinMaxValues;

    protected const string TYPE_STRING = 'string';
    protected const string TYPE_ALPHA = 'alpha';

    /** @var list<string> */
    protected const array TYPES = [
        self::TYPE_STRING,
        self::TYPE_ALPHA,
    ];

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

        $strType = Arr::getString($parameters ?? [], 'type', self::TYPE_STRING);

        if (!in_array($strType, self::TYPES)) {
            throw ValidationRuleException::invalidParameters();
        }

        /** @var 'string' | 'alpha' $strType */
        $strPattern = match ($strType) {
            self::TYPE_STRING => '.',
            self::TYPE_ALPHA => '[a-zA-Z0-9]'
        };

        [$minLen, $maxLen] = self::getMinMaxValues($parameters, 'minLen', 'maxLen');

        $lengthPattern = is_null($minLen) ? '{0,' : '{' . $minLen . ',';
        $lengthPattern .= is_null($maxLen) ? '}' : $maxLen . '}';

        $instance->pattern = '/^' . $strPattern . $lengthPattern . '$/';

        return $instance;
    }

    #[Override]
    public function validationMessage(): string
    {
        return 'The :attribute field must be a valid string.';
    }
}
