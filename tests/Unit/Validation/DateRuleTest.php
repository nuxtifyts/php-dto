<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Validation;

use Nuxtifyts\PhpDto\Validation\Rules\DateRule;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Throwable;

#[CoversClass(DateRule::class)]
final class DateRuleTest extends ValidationRuleTestCase
{
    /** 
     *  @throws Throwable
     */
    #[Test]
    public function validate_validation_message(): void
    {
        $rule = DateRule::make();

        self::assertEquals(
            'The :attribute must be a valid date.',
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
            'Will evaluate false when value is not a valid datetime string' => [
                'validationRuleClassString' => DateRule::class,
                'makeParams' => null,
                'expectedMakeException' => null,
                'valueToBeEvaluated' => 'not-a-valid-datetime-string',
                'expectedResult' => false
            ],
            'Will evaluate true when a valid datetime string is provided (Y-m-d)' => [
                'validationRuleClassString' => DateRule::class,
                'makeParams' => null,
                'expectedMakeException' => null,
                'valueToBeEvaluated' => '2021-01-01',
                'expectedResult' => true
            ],
            'Will evaluate true when a valid datetime string is provided (ATOM)' => [
                'validationRuleClassString' => DateRule::class,
                'makeParams' => null,
                'expectedMakeException' => null,
                'valueToBeEvaluated' => '2021-01-01T00:00:00+00:00',
                'expectedResult' => true
            ],
            'Will evaluate true when a valid datetime string is provided (Y-m-d H:m:s)' => [
                'validationRuleClassString' => DateRule::class,
                'makeParams' => null,
                'expectedMakeException' => null,
                'valueToBeEvaluated' => '2021-01-01 00:00:00',
                'expectedResult' => true
            ],
        ];
    }
}
