<?php

namespace Nuxtifyts\PhpDto\Normalizers;

final readonly class ArrayNormalizer extends Normalizer
{
    public function normalize(): array|false
    {
        if (
            !is_array($this->value)
            || (
                array_is_list($this->value)
                && !empty($this->value)
            )
        ) {
            return false;
        }

        $normalized = [];

        foreach ($this->value as $key => $subValue) {
            if (!is_string($key)) {
                return false;
            }

            $normalized[$key] = $subValue;
        }

        return $normalized;
    }
}
