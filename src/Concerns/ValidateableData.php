<?php

namespace Nuxtifyts\PhpDto\Concerns;

use Nuxtifyts\PhpDto\Configuration\DataConfiguration;
use Nuxtifyts\PhpDto\Contexts\ClassContext;
use Nuxtifyts\PhpDto\Exceptions\DataCreationException;
use Nuxtifyts\PhpDto\Exceptions\DataValidationException;

use Throwable;
use Exception;

trait ValidateableData
{
    /**
     * @throws DataValidationException
     */
    public static function validate(mixed $data): void
    {
        try {
            $validationConfig = DataConfiguration::getInstance()->validation;

            $validationRules = $validationConfig->validationRulesRefererClass::createInstance()
                ->getRulesFromClassContext(ClassContext::getInstance(static::class));

            $validationConfig
                ->validatorClass::createInstance($validationRules)
                ->validate($data);
        } catch (Throwable $t) {
            throw DataValidationException::invalidData($t->getMessage(), previous: $t);
        }
    }

    /**
     *  @param array<string, mixed> $data
     *
     *  @throws DataValidationException
     */
    public static function validateAndCreate(array $data): static
    {
        try {
            /** @var ClassContext<static> $context */
            $context = ClassContext::getInstance(static::class);

            $value = static::normalizeValue($args, static::class, $context->normalizers)
                ?: static::normalizeValue($args[0] ?? [], static::class, $context->normalizers);

            if ($value === false) {
                throw DataCreationException::invalidParamsPassed(static::class);
            }

            // Add Validate Data pipe
        } catch (Throwable $t) {
            throw DataValidationException::invalidData($t->getMessage(), previous: $t);
        }
    }

    public function isValid(): bool
    {
        try {
            $this::validate($this->toArray());

            return true;
        } catch (Exception) {
            return false;
        }
    }

    /**
     * @param ?array<array-key, mixed> $data
     *
     * @return array<string, mixed>
     */
    public static function validationRules(?array $data = null): array
    {
        return [];
    }
}
