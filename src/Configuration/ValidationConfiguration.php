<?php

namespace Nuxtifyts\PhpDto\Configuration;

use Nuxtifyts\PhpDto\Exceptions\DataConfigurationException;
use Nuxtifyts\PhpDto\Support\Arr;
use Nuxtifyts\PhpDto\Support\Validation\Contracts\DataValidator;
use Nuxtifyts\PhpDto\Support\Validation\Contracts\RuleReferer;
use Nuxtifyts\PhpDto\Support\Validation\ValidationRulesReferer;
use Nuxtifyts\PhpDto\Support\Validation\Validator;

class ValidationConfiguration implements Configuration
{
    protected static ?self $instance = null;

    /**
     * @param class-string<DataValidator> $validatorClass
     * @param class-string<RuleReferer> $validationRulesRefererClass
     */
    protected function __construct(
        protected(set) string $validatorClass,
        protected(set) string $validationRulesRefererClass
    ) {
    }

    /**
     * @param ?array<array-key, mixed> $config
     *
     * @throws DataConfigurationException
     */
    public static function getInstance(
        ?array $config = null,
        bool $forceCreate = false
    ): self {
        if (self::$instance && !$forceCreate) {
            return self::$instance;
        }

        $validatorClass = Arr::getString($config ?? [], 'validator', Validator::class);

        $validationRulesRefererClass = Arr::getString(
            $config ?? [],
            'ruleReferer',
            ValidationRulesReferer::class
        );

        if (
            !$validatorClass
            || !class_exists($validatorClass)
            || !is_subclass_of($validatorClass, DataValidator::class)

            || !$validationRulesRefererClass
            || !class_exists($validationRulesRefererClass)
            || !is_subclass_of($validationRulesRefererClass, RuleReferer::class)
        ) {
            throw DataConfigurationException::invalidValidationClasses();
        }

        return self::$instance = new self(
            validatorClass: $validatorClass,
            validationRulesRefererClass: $validationRulesRefererClass
        );
    }
}
