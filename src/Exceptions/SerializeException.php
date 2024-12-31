<?php

namespace Nuxtifyts\PhpDto\Exceptions;

use Exception;
use Throwable;

class SerializeException extends Exception
{
    protected const int GENERIC_ERROR_CODE = 0;
    protected const int NO_SERIALIZERS_ERROR_CODE = 1;
    protected const int UNABLE_TO_SERIALIZE_SCALAR_TYPE_ITEM_ERROR_CODE = 2;
    protected const int UNABLE_TO_SERIALIZE_BACKED_ENUM_ERROR_CODE = 3;
    protected const int UNABLE_TO_SERIALIZE_DATE_TIME_ITEM_ERROR_CODE = 4;
    protected const int UNABLE_TO_SERIALIZE_DATA_ITEM_ERROR_CODE = 5;
    protected const int UNABLE_TO_SERIALIZE_ARRAY_ITEM_ERROR_CODE = 6;

    public static function generic(?Throwable $throwable = null): self
    {
        return new self(
            message: 'An error occurred while serializing data',
            code: self::GENERIC_ERROR_CODE,
            previous: $throwable
        );
    }

    public static function unableToSerializeScalarTypeItem(): self
    {
        return new self(
            message: 'Could not serialize scalar type item',
            code: self::UNABLE_TO_SERIALIZE_SCALAR_TYPE_ITEM_ERROR_CODE
        );
    }

    public static function unableToSerializeBackedEnumItem(): self
    {
        return new self(
            message: 'Could not serialize array of BackedEnum items',
            code: self::UNABLE_TO_SERIALIZE_BACKED_ENUM_ERROR_CODE
        );
    }

    public static function unableToSerializeDateTimeItem(): self
    {
        return new self(
            message: 'Could not serialize array of DateTime items',
            code: self::UNABLE_TO_SERIALIZE_DATE_TIME_ITEM_ERROR_CODE
        );
    }

    public static function unableToSerializeDataItem(): self
    {
        return new self(
            message: 'Could not serialize array of data items',
            code: self::UNABLE_TO_SERIALIZE_DATA_ITEM_ERROR_CODE
        );
    }

    public static function unableToSerializeArrayItem(): self
    {
        return new self(
            message: 'Could not serialize array of items',
            code: self::UNABLE_TO_SERIALIZE_ARRAY_ITEM_ERROR_CODE
        );
    }
}
