<?php

namespace Nuxtifyts\PhpDto\Concerns;

use Nuxtifyts\PhpDto\Exceptions\DataValidationException;

trait ValidateableData
{
    /** 
     *  @return array<string, mixed>
     */
    public static function validationRules(): array
    {
        return [];
    }
}
