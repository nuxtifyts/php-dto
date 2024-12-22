<?php

namespace Nuxtifyts\PhpDto\Normalizers;

use StdClass;

final readonly class StdClassNormalizer extends Normalizer
{
    public function normalize(): array|false
    {
        if (!$this->value instanceof StdClass) {
            return false;
        }

        $value = (array) $this->value;

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
