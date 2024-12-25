<?php

namespace Nuxtifyts\PhpDto\Exceptions;

use Exception;
use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\DataRefiners\DataRefiner;

class InvalidRefiner extends Exception
{
    public static function from(
        DataRefiner $refiner,
        PropertyContext $property
    ): self {
        return new self(
            sprintf(
                'Refiner %s is not applicable to property %s',
                get_class($refiner),
                $property->propertyName
            )
        );
    }
}
