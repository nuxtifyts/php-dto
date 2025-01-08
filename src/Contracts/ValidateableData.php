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
    public static function validate(array $data): void;

    /** 
     *  @param array<string, mixed> $data
     * 
     *  @throws DataValidationException
     */
    public static function validateAndCreate(array $data): static;

    /**
     * @return true|array<string, array<string>>
     */
    public function isValid(): true|array;

    /** 
     * @return array<string, mixed>
     */
    public static function validationRules(): array;
}
