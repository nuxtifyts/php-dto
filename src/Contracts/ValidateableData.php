<?php

namespace Nuxtifyts\PhpDto\Contracts;

use Nuxtifyts\PhpDto\Exceptions\DataValidationException;

interface ValidateableData
{
    /**
     * @param array<string, mixed> $data
     *
     * @throws DataValidationException
     */
    public static function validate(mixed $data): void;

    /**
     *  @throws DataValidationException
     */
    public static function validateAndCreate(mixed $data): static;

    public function isValid(): bool;

    /**
     * @param ?array<array-key, mixed> $data
     *
     * @return array<string, mixed>
     */
    public static function validationRules(?array $data = null): array;
}
