<?php

namespace Nuxtifyts\PhpDto\Attributes\Property\Types;

use Attribute;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use InvalidArgumentException;
use ReflectionClass;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
class ArrayOfDateTimes
{
    /** @var array<string, ReflectionClass<DateTime|DateTimeImmutable>> */
    private static array $_dateTimeReflections = [];

    /** @var list<class-string<DateTime|DateTimeImmutable>> */
    private(set) array $dateTimes;

    /** @var array<string, ReflectionClass<DateTime|DateTimeImmutable>> */
    private(set) array $resolvedDateTimeReflections = [];

    /**
     * @param class-string<DateTime|DateTimeImmutable>|list<class-string<DateTime|DateTimeImmutable>> $dateTimes
     */
    public function __construct(
        string|array $dateTimes
    ) {
        try {
            $dateTimeArr = is_array($dateTimes) ? $dateTimes : [$dateTimes];

            if (empty($dateTimeArr)) {
                throw new InvalidArgumentException(
                    'ArrayOfDateTimes must have at least one DateTime or DateTimeImmutable'
                );
            } else {
                foreach ($dateTimeArr as $dateTime) {
                    if (!class_exists($dateTime) && !interface_exists($dateTime)) {
                        throw new InvalidArgumentException(
                            'Invalid DateTime or DateTimeImmutable passed to ArrayOfDateTimes: ' . $dateTime
                        );
                    }

                    /**
                     * @var ReflectionClass<DateTime|DateTimeImmutable> $reflectionDateTime
                     */
                    $reflectionDateTime = self::$_dateTimeReflections[$dateTime] ??= new ReflectionClass($dateTime);

                    if (!$reflectionDateTime->implementsInterface(DateTimeInterface::class)) {
                        throw new InvalidArgumentException(
                            'Non-DateTime or DateTimeImmutable passed to ArrayOfDateTimes: ' . $dateTime
                        );
                    }

                    $this->resolvedDateTimeReflections[$dateTime] = $reflectionDateTime;
                }
            }

            $this->dateTimes = is_array($dateTimes) ? $dateTimes : [$dateTimes];
        } catch (Exception) {
            throw new InvalidArgumentException(
                'Invalid DateTime or DateTimeImmutable passed to ArrayOfDateTimes'
            );
        }
    }
}
