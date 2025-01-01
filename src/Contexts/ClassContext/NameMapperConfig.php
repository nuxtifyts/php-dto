<?php

namespace Nuxtifyts\PhpDto\Contexts\ClassContext;

use Nuxtifyts\PhpDto\Enums\LetterCase;
use Nuxtifyts\PhpDto\Support\Str;

readonly class NameMapperConfig
{
    /** @var list<LetterCase> */
    protected array $from;

    /**
     * @param LetterCase|list<LetterCase> $from
     */
    public function __construct(
        LetterCase|array $from,
        protected LetterCase $to
    ) {
        $this->from = is_array($from) ? $from : [$from];
    }

    public function transform(string $value): string|false
    {
        if (Str::validateLetterCase($value, $this->to)) {
            return $value;
        }

        foreach ($this->from as $letterCase) {
            if (Str::validateLetterCase($value, $letterCase)) {
                return Str::transformLetterCase($value, $letterCase, $this->to);
            }
        }

        return false;
    }
}
