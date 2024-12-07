<?php

namespace Nuxtifyts\PhpDto\Normalizers;

use ArrayAccess;
use ReflectionClass;
use Throwable;

final readonly class ArrayAccessNormalizer extends Normalizer
{
    /**
     * @inheritDoc
     */
    public function normalize(): array|false
    {
        return $this->normalizeIterable()
            ?: $this->normalizeArrayAccess();
    }

    /**
     * @return array<string, mixed>|false
     */
    private function normalizeIterable(): array|false
    {
        if (!is_iterable($this->value)) {
            return false;
        }

        $normalized = [];

        foreach ($this->value as $key => $value) {
            if (!is_string($key)) {
                return false;
            }

            $normalized[$key] = $value;
        }

        return $normalized;
    }

    /**
     * @return array<string, mixed>|false
     */
    private function normalizeArrayAccess(): array|false
    {
        try {
            if (!$this->value instanceof ArrayAccess) {
                return false;
            }

            $reflectionClass = new ReflectionClass($this->class);

            $normalized = [];

            foreach ($reflectionClass->getProperties() as $property) {
                $normalized[$property->getName()] = $this->value[$property->getName()];
            }

            return $normalized;
        } catch (Throwable) {
            return false;
        }
    }
}
