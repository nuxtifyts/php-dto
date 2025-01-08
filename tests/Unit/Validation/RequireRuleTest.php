<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Validation;

use Nuxtifyts\PhpDto\Validation\Rules\RequiredRule;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Throwable;

#[CoversClass(RequiredRule::class)]
final class RequireRuleTest extends ValidationRuleUnitCase
{
    /** 
     *  @throws Throwable
     */
    #[Test]
    public function validate_validation_message(): void
    {
        self::assertEquals(
            'The :attribute field is required.',
            RequiredRule::make()->validationMessage()
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
            'Will return false if the value is empty string' => [
                'validationRuleClassString' => RequiredRule::class,
                'makeParams' => null,
                'expectedMakeException' => null,
                'valueToBeEvaluated' => '',
                'expectedResult' => false,
            ],
            'Will return false if the value is null' => [
                'validationRuleClassString' => RequiredRule::class,
                'makeParams' => null,
                'expectedMakeException' => null,
                'valueToBeEvaluated' => null,
                'expectedResult' => false,
            ],
            'Will return false if the value is falsy' => [
                'validationRuleClassString' => RequiredRule::class,
                'makeParams' => null,
                'expectedMakeException' => null,
                'valueToBeEvaluated' => false,
                'expectedResult' => false,
            ],
            'Will return true otherwise' => [
                'validationRuleClassString' => RequiredRule::class,
                'makeParams' => null,
                'expectedMakeException' => null,
                'valueToBeEvaluated' => 'Something',
                'expectedResult' => true,
            ]
        ];
    }
}
