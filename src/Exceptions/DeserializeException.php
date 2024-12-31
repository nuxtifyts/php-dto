<?php

namespace Nuxtifyts\PhpDto\Exceptions;

use Exception;
use Throwable;

class DeserializeException extends Exception
{
    protected const int GENERIC_ERROR_CODE = 0;
    protected const int INVALID_VALUE_ERROR_CODE = 1;
    protected const int PROPERTY_IS_NOT_NULLABLE_ERROR_CODE = 2;
    protected const int UNABLE_TO_DESERIALIZE_SCALAR_TYPE_ITEM_ERROR_CODE = 3;
    protected const int UNABLE_TO_DESERIALIZE_BACKED_ENUM_ITEM_ERROR_CODE = 4;
    protected const int UNABLE_TO_DESERIALIZE_DATE_TIME_ITEM_ERROR_CODE = 5;
    protected const int UNABLE_TO_DESERIALIZE_DATA_ITEM_ERROR_CODE = 6;
    protected const int UNABLE_TO_DESERIALIZE_ARRAY_ITEM_ERROR_CODE = 7;
    protected const int INVALID_PARAMS_PASSED_ERROR_CODE = 8;

    public static function generic(?Throwable $throwable = null): self
    {
        return new self(
            message: 'An error occurred while deserializing data',
            code: self::GENERIC_ERROR_CODE,
            previous: $throwable
        );
    }

    public static function invalidValue(): self
    {
        return new self(
            message: 'Invalid value passed to from method',
            code: self::INVALID_VALUE_ERROR_CODE
        );
    }

    public static function propertyIsNotNullable(): self
    {
        return new self(
            message: 'Property is not nullable',
            code: self::PROPERTY_IS_NOT_NULLABLE_ERROR_CODE
        );
    }

    public static function unableToDeserializeScalarTypeItem(): self
    {
        return new self(
            message: 'Could not deserialize scalar type item',
            code: self::UNABLE_TO_DESERIALIZE_SCALAR_TYPE_ITEM_ERROR_CODE
        );
    }

    public static function unableToDeserializeBackedEnumItem(): self
    {
        return new self(
            message: 'Could not deserialize BackedEnum item',
            code: self::UNABLE_TO_DESERIALIZE_BACKED_ENUM_ITEM_ERROR_CODE
        );
    }

    public static function unableToDeserializeDateTimeItem(): self
    {
        return new self(
            message: 'Could not deserialize DateTime item',
            code: self::UNABLE_TO_DESERIALIZE_DATE_TIME_ITEM_ERROR_CODE
        );
    }

    public static function unableToDeserializeDataItem(): self
    {
        return new self(
            message: 'Could not deserialize Data item',
            code: self::UNABLE_TO_DESERIALIZE_DATA_ITEM_ERROR_CODE
        );
    }

    public static function unableToDeserializeArrayItem(): self
    {
        return new self(
            message: 'Could not deserialize array item',
            code: self::UNABLE_TO_DESERIALIZE_ARRAY_ITEM_ERROR_CODE
        );
    }

    public static function invalidParamsPassed(): self
    {
        return new self(
            message: 'Invalid params passed',
            code: self::INVALID_PARAMS_PASSED_ERROR_CODE
        );
    }
}
