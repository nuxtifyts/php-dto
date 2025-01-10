<?php

namespace Nuxtifyts\PhpDto\Tests\Unit\Validation;

use Throwable;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use Nuxtifyts\PhpDto\Validation\Rules\RegexRule;
use Nuxtifyts\PhpDto\Validation\Rules\ValidationRule;
use Nuxtifyts\PhpDto\Exceptions\ValidationRuleException;

#[CoversClass(RegexRule::class)]
#[CoversClass(ValidationRuleException::class)]
class RegexRuleTest extends ValidationRuleTestCase
{
    /** 
     *  @throws Throwable
     */
    #[Test]
    public function validate_validation_message(): void
    {
        $rule = RegexRule::make(['pattern' => '/^test$/']);

        self::assertEquals(
            'The :attribute field does not match the required pattern.',
            $rule->validationMessage()
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
            'Will evaluate false when value is not a valid regex string' => [
                'validationRuleClassString' => RegexRule::class,
                'makeParams' => ['pattern' => '/^test$/'],
                'expectedMakeException' => null,
                'valueToBeEvaluated' => 'not-a-valid-regex-string',
                'expectedResult' => false
            ],
            'Will evaluate true when a valid regex string is provided' => [
                'validationRuleClassString' => RegexRule::class,
                'makeParams' => ['pattern' => '/^test$/'],
                'expectedMakeException' => null,
                'valueToBeEvaluated' => 'test',
                'expectedResult' => true
            ],
            'Will evaluate false when a non string value is provided' => [
                'validationRuleClassString' => RegexRule::class,
                'makeParams' => ['pattern' => '/^test$/'],
                'expectedMakeException' => null,
                'valueToBeEvaluated' => 123,
                'expectedResult' => false
            ],
            'Will throw an exception if the pattern is not a valid regex' => [
                'validationRuleClassString' => RegexRule::class,
                'makeParams' => ['pattern' => '/^test'],
                'expectedMakeException' => ValidationRuleException::class,
                'valueToBeEvaluated' => 'test',
                'expectedResult' => false
            ],
        ];
    }
}
