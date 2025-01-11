<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Validation\Rules;

use Nuxtifyts\PhpDto\Exceptions\ValidationRuleException;
use Nuxtifyts\PhpDto\Validation\Rules\DateRule;
use Nuxtifyts\PhpDto\Validation\Rules\ValidationRule;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Throwable;

#[CoversClass(DateRule::class)]
#[CoversClass(ValidationRuleException::class)]
final class DateRuleTest extends ValidationRuleTestCase
{
    /** 
     *  @throws Throwable
     */
    #[Test]
    public function validate_validation_message(): void
    {
        self::assertEquals(
            'The :attribute field must be a valid date.',
            DateRule::make()->validationMessage()
        );

        self::assertEquals(
            'The :attribute field must be a valid date in one of the following formats: Y/m-d H/m/s',
            DateRule::make(['formats' => ['Y/m-d H/m/s']])->validationMessage()
        );
    }

    /**
     * @return array<string, array{
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
            'Will evaluate false when a custom datetime string is provided but no formats are set' => [
                'validationRuleClassString' => DateRule::class,
                'makeParams' => null,
                'expectedMakeException' => null,
                'valueToBeEvaluated' => '2021/01-01 00/00/00',
                'expectedResult' => false
            ],
            'Will evaluate true when a custom datetime string is provided and a format is set' => [
                'validationRuleClassString' => DateRule::class,
                'makeParams' => ['formats' => ['Y/m-d H/m/s']],
                'expectedMakeException' => null,
                'valueToBeEvaluated' => '2021/01-01 00/00/00',
                'expectedResult' => true
            ],
            'Will throw an exception when an invalid non string format is passed' => [
                'validationRuleClassString' => DateRule::class,
                'makeParams' => ['formats' => ['Y/m-d H/m/s', 123]],
                'expectedMakeException' => ValidationRuleException::class,
                'valueToBeEvaluated' => '2021/01-01 00/00/00',
                'expectedResult' => false
            ],
        ];
    }
}
