<?php

namespace Nuxtifyts\PhpDto\DataRefiners;

use DateTimeInterface;
use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\Enums\Property\Type;
use Nuxtifyts\PhpDto\Exceptions\InvalidRefiner;
use DateTimeImmutable;

class DateTimeRefiner implements DataRefiner
{
    /** @var list<string> */
    protected(set) array $formats = [
        DateTimeInterface::ATOM,
        'Y-m-d H:i:s',
        'Y-m-d'
    ];

    /**
     * @param string|list<string>|null $formats
     */
    public function __construct(
        string|array|null $formats = null
    ) {
        if (!is_null($formats)) {
            if (is_string($formats)) {
                $this->formats[] = $formats;
            } else {
                $this->formats = array_values(array_unique([
                    ...$this->formats,
                    ...$formats
                ]));
            }
        }
    }

    public function refine(mixed $value, PropertyContext $property): mixed
    {
        if (is_null($value)) {
            return null;
        }

        if (is_string($value)) {
            $typeContexts = $property->getFilteredTypeContexts(Type::DATETIME);

            if (empty($typeContexts)) {
                throw InvalidRefiner::from($this, $property);
            }

            $refinedValue = false;

            if (array_any(
                $this->formats,
                static function(string $format) use (&$refinedValue, $value): bool {
                    return (bool)($refinedValue = DateTimeImmutable::createFromFormat($format, $value));
                }
            )) {
                return $refinedValue;
            }
        }

        return $value;
    }
}
