<?php

namespace Nuxtifyts\PhpDto\Attributes\PropertyAttributes\Types;

use Attribute;
use Exception;
use InvalidArgumentException;
use Nuxtifyts\PhpDto\Contracts\BaseData as BaseDataContract;
use Nuxtifyts\PhpDto\Data;
use ReflectionClass;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class ArrayOfData
{
    /** @var ReflectionClass */
    private static array $_dataReflections = [];

    /** @var list<class-string<Data>> */
    private(set) public array $dataClasses;

    /** @var ReflectionClass */
    private(set) public array $resolvedDataReflections = [];

    /**
     * @param class-string<Data>|list<class-string<Data>> $dataClasses
     */
    public function __construct(
        string|array $dataClasses
    ) {
        try {
            $dataArr = is_array($dataClasses) ? $dataClasses : [$dataClasses];

            if (empty($dataArr)) {
                throw new InvalidArgumentException(
                    'ArrayOfData must have at least one Data class'
                );
            } else {
                foreach ($dataArr as $data) {
                    if (!class_exists($data)) {
                        throw new InvalidArgumentException(
                            'Invalid Data class passed to ArrayOfData: ' . $data
                        );
                    }

                    /**
                     * @var ReflectionClass<Data> $reflectionData
                     */
                    $reflectionData = self::$_dataReflections[$data] ??= new ReflectionClass($data);

                    if (!$reflectionData->implementsInterface(BaseDataContract::class)) {
                        throw new InvalidArgumentException(
                            'Non-Data class passed to ArrayOfData: ' . $data
                        );
                    }

                    $this->resolvedDataReflections[$data] = $reflectionData;
                }
            }

            $this->dataClasses = is_array($dataClasses) ? $dataClasses : [$dataClasses];
        } catch (Exception) {
            throw new InvalidArgumentException(
                'Invalid Data class passed to ArrayOfData'
            );
        }
    }
}
