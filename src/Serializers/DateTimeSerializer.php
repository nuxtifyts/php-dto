<?php

namespace Nuxtifyts\PhpDto\Serializers;

use ArrayAccess;
use Nuxtifyts\PhpDto\Contexts\PropertyContext;
use Nuxtifyts\PhpDto\Enums\Property\Type;
use DateTimeInterface;
use Nuxtifyts\PhpDto\Exceptions\DeserializeException;
use Nuxtifyts\PhpDto\Exceptions\SerializeException;
use Exception;
use DateTimeImmutable;

class DateTimeSerializer extends Serializer
{
    /**
     * @inheritDoc
     */
    public static function supportedTypes(): array
    {
        return [
            Type::DATETIME
        ];
    }

    /**
     * @inheritDoc
     */
    public function serialize(PropertyContext $property, object $object): array
    {
        $value = $property->getValue($object);

        // TODO: move nullable value outside because it's repetitive
        return [
            $property->propertyName => match(true) {
                $value instanceof DateTimeInterface => $value->format(DateTimeInterface::ATOM),
                $value === null && $property->isNullable => null,
                default => throw new SerializeException('Value is not an instance of DateTimeInterface')
            }
        ];
    }

    /**
     * @inheritDoc
     */
    public function deserialize(PropertyContext $property, ArrayAccess|array $data): ?DateTimeInterface
    {
        $value = $data[$property->propertyName] ?? null;

        if (is_string($value)) {
            try {
                foreach ($property->dateTimeReflections as $dateTimeReflection) {
                    if ($dateTimeReflection->isInterface()) {
                        return new DateTimeImmutable($value);
                    } else {
                        $value = $dateTimeReflection->newInstance($value);

                        if (!$value instanceof DateTimeInterface) {
                            throw new Exception('Not an instance of DateTimeInterface');
                        }

                        return $value;
                    }
                }
            // @codeCoverageIgnoreStart
            } catch (Exception) {}
            // @codeCoverageIgnoreEnd
        }

        return is_null($value) && $property->isNullable
            ? null
            : throw new DeserializeException('Could not deserialize DateTimeInterface');
    }
}
