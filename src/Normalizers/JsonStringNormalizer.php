<?php

namespace Nuxtifyts\PhpDto\Normalizers;

final readonly class JsonStringNormalizer extends Normalizer
{
    public function normalize(): array|false
    {
        if (!is_string($this->value)) {
            return false;
        }

        $value = json_decode($this->value, true);

        if (
            json_last_error() !== JSON_ERROR_NONE
            || !is_iterable($value)
        ) {
            return false;
        }

        $normalized = [];

        foreach ($value as $key => $subValue) {
            if (!is_string($key)) {
                return false;
            }

            $normalized[$key] = $subValue;
        }

        return $normalized;
    }
}
