<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Validation;

use Nuxtifyts\PhpDto\Validation\Rules\BackedEnumRule;
use Nuxtifyts\PhpDto\Exceptions\ValidationRuleException;
use Nuxtifyts\PhpDto\Validation\Rules\ValidationRule;
use Nuxtifyts\PhpDto\Tests\Dummies\Enums\YesNoBackedEnum;
use Nuxtifyts\PhpDto\Tests\Dummies\Enums\YesNoEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\UsesClass;
use Throwable;

#[CoversClass(BackedEnumRule::class)]
#[CoversClass(ValidationRuleException::class)]
#[UsesClass(YesNoBackedEnum::class)]
#[UsesClass(YesNoEnum::class)]
final class BackedEnumRuleTest extends ValidationRuleUnitCase
{
    /** 
     *  @throws Throwable
     */
    #[Test]
    public function validate_validation_message(): void
    {
        $rule = BackedEnumRule::make([
            'backedEnumClass' => YesNoBackedEnum::class
        ]);

        self::assertEquals(
            'The :attribute field is invalid.',
            $rule->validationMessage()
        );

        $rule = BackedEnumRule::make([
            'backedEnumClass' => YesNoBackedEnum::class,
            'allowedValues' => $allowedValues = [YesNoBackedEnum::YES]
        ]);

        $allowedValues = implode(
            ', ', 
            array_map(static fn (YesNoBackedEnum $value) => $value->value, $allowedValues)
        );

        self::assertEquals(
            "The :attribute field must be one of the following values: $allowedValues.",
            $rule->validationMessage()
        );
    }

    /** 
     *  @return array<string, array{
     *     validationRuleClassString: class-string<ValidationRule>,
     *     makeParams: ?array<string, mixed>,
     *     expectedMakeException: ?class-string<ValidationRuleException>,
     *     valueToBeEvaluated: mixed,
     *     expectedResult: bool
     * }>
     */
    public static function data_provider(): array
    {
        return [
            'Will_throw_an_exception_if_no_enum_class_is_passed' => [
                'validationRuleClassString' => BackedEnumRule::class,
                'makeParams' => [],
                'expectedMakeException' => ValidationRuleException::class,
                'valueToBeEvaluated' => '',
                'expectedResult' => false,
            ],
            'Will_throw_an_exception_if_invalid_backed_enum_class_is_passed' => [
                'validationRuleClassString' => BackedEnumRule::class,
                'makeParams' => [
                    'backedEnumClass' => 'InvalidEnumClass'
                ],
                'expectedMakeException' => ValidationRuleException::class,
                'valueToBeEvaluated' => '',
                'expectedResult' => false,
            ],
            'Will_throw_an_exception_if_invalid_non_backed_enum_class_is_passed' => [
                'validationRuleClassString' => BackedEnumRule::class,
                'makeParams' => [
                    'backedEnumClass' => YesNoEnum::class
                ],
                'expectedMakeException' => ValidationRuleException::class,
                'valueToBeEvaluated' => '',
                'expectedResult' => false,
            ],
            'Will return false if the value is invalid backed enum value' => [
                'validationRuleClassString' => BackedEnumRule::class,
                'makeParams' => [
                    'backedEnumClass' => YesNoBackedEnum::class
                ],
                'expectedMakeException' => null,
                'valueToBeEvaluated' => 'Something',
                'expectedResult' => false,
            ],
            'Will return false if the value is neither a backed enum, nor a string or an integer' => [
                'validationRuleClassString' => BackedEnumRule::class,
                'makeParams' => [
                    'backedEnumClass' => YesNoBackedEnum::class
                ],
                'expectedMakeException' => null,
                'valueToBeEvaluated' => [],
                'expectedResult' => false,
            ],
            'Will return true if the value is a valid backed enum value' => [
                'validationRuleClassString' => BackedEnumRule::class,
                'makeParams' => [
                    'backedEnumClass' => YesNoBackedEnum::class
                ],
                'expectedMakeException' => null,
                'valueToBeEvaluated' => 'yes',
                'expectedResult' => true,
            ],
            'Will return false if the value is not within the allowed values' => [
                'validationRuleClassString' => BackedEnumRule::class,
                'makeParams' => [
                    'backedEnumClass' => YesNoBackedEnum::class,
                    'allowedValues' => [YesNoBackedEnum::NO]
                ],
                'expectedMakeException' => null,
                'valueToBeEvaluated' => 'yes',
                'expectedResult' => false,
            ],
            'Will return true if the value passed if an actual backed enum of the same instance' => [
                'validationRuleClassString' => BackedEnumRule::class,
                'makeParams' => [
                    'backedEnumClass' => YesNoBackedEnum::class
                ],
                'expectedMakeException' => null,
                'valueToBeEvaluated' => YesNoBackedEnum::YES,
                'expectedResult' => true,
            ]
        ];
    }
}
