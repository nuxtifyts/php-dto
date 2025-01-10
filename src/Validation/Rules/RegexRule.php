<?php

namespace Nuxtifyts\PhpDto\Validation\Rules;

use Nuxtifyts\PhpDto\Exceptions\ValidationRuleException;

class RegexRule implements ValidationRule
{
    protected string $pattern;

    /** @var 0 | 256 | 512 | 768 */
    protected int $flags = 0;

    protected int $offset = 0;

    public string $name {
        get {
            return 'regex';
        }
    }

    public function evaluate(mixed $value): bool
    {
        return is_string($value) 
            && preg_match(
                pattern: $this->pattern, 
                subject: $value,
                flags: $this->flags,
                offset: $this->offset
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

        $pattern = $parameters['pattern'] ?? null;

        if (!is_string($pattern) || @preg_match($pattern, '') === false) {
            throw ValidationRuleException::invalidParameters();
        }

        $instance->pattern = $pattern;

        return $instance;
    }

    public function validationMessage(): string
    {
        return 'The :attribute field does not match the required pattern.';
    }
}
